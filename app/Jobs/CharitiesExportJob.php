<?php

namespace App\Jobs;

use App\Models\Charity;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CharitiesExportJob implements ShouldQueue
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
    public function __construct($history_id, $filters)
    {

        $this->history_id = $history_id;
        $this->filters = $filters;

        // File 
        $this->filename = 'export_charities_'.now()->format("Y-m-d-his").".csv";


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

        $header = ['BN', 'Name', 'Status', 'Type of Donee', 'Effective Date', 
                    'Designation', 'Type', 'Category', 'Address', 'City', 'Province', 'Country', 'Postal', 
                    'Use Alt Address', 'Alt Address 1', 'Alt Address 2','Alt City', 'Alt Province', 'Alt Country', 'Alt Postal',
                    'Financial Contact Name','Financial Contact Title','Financial Contact Email'
                  ];

        $fields = ['registration_number','charity_name','charity_status','type_of_qualified_donee','effdt',
                   'designation_name','charity_type','category_name','address','city','province','country','postal_code',
                   'use_alt_address','alt_address1','alt_address2','alt_city','alt_province','alt_country','alt_postal_code',
                   'financial_contact_name','financial_contact_title','financial_contact_email'
                ];
    

        // Export header
        fputcsv($handle, ['Report Title     :  Eligible Employee Report'] );
        fputcsv($handle, ['Report Run on    : ' . now() ] );
        fputcsv($handle, [''] );
        fputcsv($handle, $header );

        $filters = $this->filters;

        $sql = Charity::when( $filters['registration_number'], function($query) use($filters) {
                        $query->where('charities.registration_number', 'like', '%'. $filters['registration_number'] .'%');
                    })
                    ->when( $filters['charity_name'], function($query) use($filters) {
                        $query->where('charities.charity_name', 'like', '%'. $filters['charity_name'] .'%');
                    })
                    ->when( $filters['charity_status'], function($query) use($filters) {
                        $query->where('charities.charity_status', 'like', '%'. $filters['charity_status'] .'%');
                    })
                    ->when( $filters['effdt'], function($query) use($filters) {
                        $query->where('charities.effective_date_of_status', '>=', $filters['effdt']);
                    })
                    ->when( $filters['designation_code'], function($query) use($filters) {
                        $query->where('charities.designation_code', $filters['designation_code']);
                    })
                    ->when( $filters['category_code'], function($query) use($filters) {
                        $query->where('charities.category_code', $filters['category_code'] );
                    })
                    ->when( $filters['province'], function($query) use($filters) {
                        $query->where('charities.province', $filters['province']);
                    })
                    ->when( $request->use_alt_address == 'Y', function($query) use($request) {
                        $query->where('use_alt_address', '1');
                    })
                    ->when( $request->use_alt_address == 'N', function($query) use($request) {
                        $query->where(function($q) {
                            $q->where('use_alt_address', '0')
                              ->orWhereNull('use_alt_address');
                        });
                    })
                    ->orderBy('charity_name');

        // add 
        $total_count = $sql->count();

        \App\Models\ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
           'status' => 'Processing',
           'original_filename' => $this->filename,
           'filename' => $this->filename,
           'total_count' => $total_count,
           'start_at' => now(),
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
                \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[                    
                   'status' => 'Processing',
                   'done_count' => $count,
                ]);

        });

        fclose($handle);

        \App\Models\ProcessHistory::UpdateOrCreate([
            'id' => $this->history_id,
        ],[                    
           'status' => 'Completed',
           'end_at' => now(),
        ]);
    }
}
