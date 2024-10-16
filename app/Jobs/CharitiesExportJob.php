<?php

namespace App\Jobs;

use App\Models\FSPool;
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

            'f_s_pool_count',
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

        $sql = Charity::select('charities.*', 
                        DB::Raw("CONCAT(charities.designation_code, char(9)) as designation"),
                                DB::Raw("CONCAT(charities.category_code, char(9)) as category"),
                                DB::Raw(" '' as region_code"),
                                DB::Raw(" '' as region_name"),
                            DB::Raw(" '' as percentage"),
                            DB::Raw(" '' as supported_program"),
                            DB::Raw(" '' as contact_title"),
                            DB::Raw(" '' as contact_name"),
                            DB::Raw(" '' as contact_email"),
                            'charities.comments',
                            DB::Raw(" 0 as f_s_pool_count"),
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
           'status' => 'Processing',                    
           'total_count' => $total_count,
        ]);
        

        // export the data with filter selection
        $count = 0;
        $sql->chunk(2000, function($charities) use ($handle, $fields, &$count, $filters) {
         
                // additional data 
                foreach( $charities as $charity) {
                    // $charity->business_unit_name = $charity->bus_unit->name;
                    // $charity->region_name = $charity->region->name;
                    $charity->effdt = $charity->effective_date_of_status ?  $charity->effective_date_of_status->format('m-d-Y') : null;

                    // FS Pool Information
                    $pools = FSPool::asOfDate( $filters['as_of_date'])
                                        ->join('f_s_pool_charities', 'f_s_pools.id', 'f_s_pool_charities.f_s_pool_id')
                                        ->join('regions', 'f_s_pools.region_id', 'regions.id')
                                        ->where('f_s_pool_charities.charity_id', $charity->id)
                                        ->where('f_s_pools.status', 'A')
                                        ->select(
                                            DB::Raw("CONCAT(regions.code, char(9)) as region_code"),
                                           'regions.name as region_name',
                                           'f_s_pool_charities.percentage', 'f_s_pool_charities.name as supported_program',
                                           'f_s_pool_charities.contact_title', 'f_s_pool_charities.contact_name', 'f_s_pool_charities.contact_email',
                                        )->get();

                    if ( $pools) {
                        $charity->f_s_pool_count = $pools->count();

                        foreach ($pools as $key=>$pool) {
                     
                            $newline = ($pools->count() > 1 && $key + 1 < $pools->count()) ? PHP_EOL : '';

                            $charity->region_code .= $pool->region_code . $newline;
                            $charity->region_name .= $pool->region_name . $newline; 
                            $charity->percentage .= $pool->percentage . $newline;
                            $charity->supported_program .= $pool->supported_program . $newline;
                            $charity->contact_title .= $pool->contact_title . $newline;
                            $charity->contact_name .= $pool->contact_name . $newline;
                            $charity->contact_email .= $pool->contact_email . $newline;
                        }
                    }
                }

                $subset = $charities->map->only( $fields );

                // output to csv
                foreach($subset as $charity) {
                    fputcsv($handle, $charity, ',', '"' );
                }

                // update done count
                $count = $count + count($charities);
                if (($count % 6000) == 0) {
                    ProcessHistory::UpdateOrCreate([
                       'id' => $this->history_id,
                    ],[                    
                        'done_count' => $count,
                    ]);
                }

        });

        fclose($handle);

        // Update the data at the end
        ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
           'done_count' => $count, 
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
