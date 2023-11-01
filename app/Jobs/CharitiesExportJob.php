<?php

namespace App\Jobs;

use App\Models\Charity;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ProcessHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;

ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class CharitiesExportJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $history_id;
    protected $filters;     // array of $this->filters['all() 
    protected $filename;
    protected $uploadFilePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($history_id, $filename, $filters)
    {

        $this->history_id = $history_id;
        $this->filename = $filename;
        $this->filters = $filters;

        // File 
        // $this->filename = 'export_charities_'.now()->format("Y-m-d-his").".csv";


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $handle = fopen( storage_path('app/public/'.$this->filename), 'w');

        $header = [   
                    'CRA Org Name', 
                    'CRA Business Number', 
                    'Status', 
                    'Effective Date',
                    'Sanction', 
                    'Designation',
                    'Category', 
                    'Address', 
                    'City', 
                    'Province', 
                    'Postal',                         
                    'Country', 
                    'Use Alt Address', 
                    'Alt Address 1',
                    'Alt Address 2',
                    'Alt City',
                    'Alt Province',
                    'Alt Postal',
                    'Alt Country',
                    
                    'Financial Contact Name',
                    'Financial Contact Title',
                    'Financial Contact Email',
                    'phone',
                    'fax',

                    'ongoing_program',
                    'url',

                    'Fund Support Pool',
                    'FSP Region',
                    'FSP Region Descr',
                    'FSP Allocation (%)',
                    'FSP Supported Program',
                    'Program contact name',
                    'Program contact title',
                    'program contact email',

                    'Created At',
                    'Updated At',
                    'Notes',
                ];

        // $fields = ['registration_number','charity_name','charity_status','type_of_qualified_donee','effdt',
        //            'designation_name','charity_type','category_name','address','city','province','country','postal_code',
        //            'use_alt_address','alt_address1','alt_address2','alt_city','alt_province','alt_country','alt_postal_code',
        //            'financial_contact_name','financial_contact_title','financial_contact_email'
        //         ];

        $fields = [
            'charity_name',
            'registration_number',
            'charity_status',
            'effective_date_of_status',
            'sanction',
            'designation',   // 'designation_code',
            'category',      // 'category_code',

            'address',
            'city',
            'province',
            'postal_code',
            'country',
            
            'use_alt_address',
            'alt_address1',
            'alt_address2',
            'alt_city',
            'alt_province',
            'alt_postal_code',
            'alt_country',

            'financial_contact_name',
            'financial_contact_title',
            'financial_contact_email',
            'phone',
            'fax',

            'ongoing_program',
            'url',  

            'f_s_pool_flag',
            'region_code',
            'region_name',
            'percentage',
            'supported_program',

            'contact_name',
            'contact_title',
            'contact_email',
                  
            'created_at',
            'updated_at',
            'notes',
        ];
    

        // Export header
        fputcsv($handle, ['Report Title     :  CRA Charity Report'] );
        fputcsv($handle, ['As of Date       : ' . $this->filters['as_of_date'] ] );
        fputcsv($handle, ['Report Run on    : ' . now() ] );
        fputcsv($handle, [''] );
        fputcsv($handle, $header );

        $filters = $this->filters;

        $sql = Charity::leftJoin('f_s_pool_charities', 'f_s_pool_charities.charity_id','charities.id')
                        ->leftJoin('f_s_pools', 'f_s_pools.id', 'f_s_pool_charities.f_s_pool_id')
                        ->leftJoin('regions', 'f_s_pools.region_id', 'regions.id')
                        ->where( function($query) use($filters) {
                            $query->where('f_s_pools.start_date', '=', function ($q) use($filters) {
                                $q->selectRaw('max(start_date)')
                                    ->from('f_s_pools as A')
                                    ->whereColumn('A.region_id', 'f_s_pools.region_id')
                                    ->whereNull('A.deleted_at')
                                    ->where('A.start_date', '<=', $filters['as_of_date'] );
                            })
                            ->orWhereNull('f_s_pools.start_date');
                        })
                        ->whereNull('f_s_pool_charities.deleted_at')
                        ->whereNull('f_s_pools.deleted_at')
                        ->whereNull('regions.deleted_at')
                        ->select('charities.*', 
                        DB::Raw("CONCAT(charities.designation_code, char(9)) as designation"),
                                DB::Raw("CONCAT(charities.category_code, char(9)) as category"),
                                DB::Raw("CONCAT(regions.code, char(9)) as region_code"),
                                'regions.name as region_name',
                            'f_s_pool_charities.percentage', 'f_s_pool_charities.name as supported_program',
                            'f_s_pool_charities.contact_title', 'f_s_pool_charities.contact_name', 'f_s_pool_charities.contact_email',
                            'charities.comments',
                            DB::Raw("case when f_s_pool_id is null then 'No' else 'Yes' end as f_s_pool_flag"),
                    );
       
        // Update Process history before counting 
        ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
            'status' => 'Processing',
            'original_filename' => $this->filename,
            'filename' => $this->filename,
            'start_at' => now(),
        ]);

        // Update Process history after the total count 
        $total_count = $sql->count();
        ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
           'total_count' => $total_count,
        ]);
        

        // export the data with filter selection
        $count = 0;
        $sql->chunk(2000, function($charities) use ($handle, $fields, &$count) {
         
                // additional data 
                foreach( $charities as $charity) {
                    // $charity->business_unit_name = $charity->bus_unit->name;
                    // $charity->region_name = $charity->region->name;
                    $charity->effdt = $charity->effective_date_of_status ?  $charity->effective_date_of_status->format('m-d-Y') : null;
                }

                $subset = $charities->map->only( $fields );

                // output to csv
                foreach($subset as $charity) {
                    fputcsv($handle, $charity, ',', '"' );
                }

                // update done count
                $count = $count + count($charities);
                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[                    
                   'status' => 'Processing',
                   'done_count' => $count,
                ]);

        });

        fclose($handle);

        // Update the data at the end
        ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
           'status' => 'Completed',
           'end_at' => now(),
        ]);

        
        // Clean Up files over 14 days
        $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
        $prcs = ProcessHistory::where('id', $this->history_id)->first();

        $file_names = ProcessHistory::where('process_name', $prcs->process_name)
                        ->whereBetween('updated_at', [ today()->subdays( $retention_days + 90), today()->subdays( $retention_days + 1), ])
                        ->pluck('filename')
                        ->toArray();

        Storage::disk('public')->delete( $file_names );

    }

    public function uniqueId()
    {
        return $this->history_id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        echo "The job (CharitiesExportJob) with process history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
