<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PledgeHistory;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use App\Models\PledgeHistoryVendor;
use Illuminate\Support\Facades\Http;

class ImportPledgeHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportPledgeHistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the Pledge Hostory data from BI';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status','Initiated'
        ]);
        
        $this->info( now() );    
        $this->info("Step - 1 : Create - Pledge History Vendor");
        $this->UpdatePledgeHistoryVendor();

        $this->info( now() );    
        $this->info("Step - 2 : Create - Pledge History");
        $this->UpdatePledgeHistory();

        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();

        return 0;

    }

    protected function UpdatePledgeHistoryVendor() 
    {

        // Truncate Pledge History table
        PledgeHistoryVendor::truncate();

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
        ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
        ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_VNDR_BI_ENDPOINT') .'?$count=true&$top=1');

        $row_count = json_decode($response->body())->{'@odata.count'};
        
        $size = 10000;
        for ($i = 0; $i <= $row_count / $size ; $i++) {

            $top  = $size;
            $skip = $size * $i;
            // Loading pledge history data
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_VNDR_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip) ;

            $this->info( 'Total Count ='. $row_count .' $i ='. $i .' $top ='. $top .' $skip '. $skip);
            // Loading pledge history data
            // $response = Http::withHeaders(['Content-Type' => 'application/json'])
            //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value; 
                $batches = array_chunk($data, 1000);
                
                foreach ($batches as $key => $batch) {
                    $this->info( '    -- each batch (1000) $key - '. $key );
                    foreach ($batch as $row) {

                        // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                        // $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first(); 
                        // $charity = \App\Models\Charity::where('registration_number', $row->charity_bn)->first();
                        // $campaign_year = \App\Models\CampaignYear::where('calendar_year', $row->yearcd)->first();

                        PledgeHistoryVendor::Create([
                            'charity_bn' => $row->canada_bn,
                            'eff_status' => $row->eff_status,
                            'effdt' => $row->effdt,
                            'name1' => $row->name1,
                            'name2' => $row->name2,
                            'tgb_reg_district' => $row->tgb_reg_district,
                            'vendor_id' => $row->vendor_id,
                            'yearcd' => $row->yearcd,
                        ]);
                    }
                }
            } else {
                $this->info( $response->status() );
                $this->info( $response->body() );
            }

        }
    }


    protected function UpdatePledgeHistory() 
    {
        // Truncate Pledge History table
        PledgeHistory::truncate();

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT') .'?$count=true&$top=1');

        $row_count = json_decode($response->body())->{'@odata.count'};
        
        $size = 10000;
        for ($i = 0; $i <= $row_count / $size ; $i++) {

            $top  = $size;
            $skip = $size * $i;
            // Loading pledge history data
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip) ;

            $this->info( 'Total Count ='. $row_count .' $i ='. $i .' $top ='. $top .' $skip '. $skip);
            // Loading pledge history data
            // $response = Http::withHeaders(['Content-Type' => 'application/json'])
            //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value; 
                $batches = array_chunk($data, 1000);
                
                foreach ($batches as $key => $batch) {
                    $this->info( '    -- each batch (1000) $key - '. $key );
                    foreach ($batch as $row) {

                        // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                        $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first(); 
                        $charity = \App\Models\Charity::where('registration_number', $row->charity_bn)->first();
                        $campaign_year = \App\Models\CampaignYear::where('calendar_year', $row->yearcd)->first();

                        $vendor = PledgeHistoryVendor::where('tgb_reg_district', $row->tgb_reg_district)
                                    ->where('charity_bn',  $row->charity_bn)
                                    ->where('effdt', function($query) use($row) {
                                        return $query->selectRaw('max(effdt)')
                                                ->from('pledge_history_vendors as A')
                                                ->whereColumn('A.tgb_reg_district', 'pledge_history_vendors.tgb_reg_district')
                                                ->whereColumn('A.charity_bn', 'pledge_history_vendors.charity_bn')
                                                ->where('A.effdt', '<=', $row->yearcd . '-12-31');
                                    })->first();

                        PledgeHistory::Create([
                            'campaign_type' => $row->campaign_type,
                            'source' => $row->source,
                            'tgb_reg_district' => $row->tgb_reg_district,
                            'region_id' => $region ? $region->id : null,
                            'charity_bn' => $row->charity_bn,
                            'charity_id' => $charity ? $charity->id : null,
                            'yearcd' => $row->yearcd,
                            'name1' => $vendor ? $vendor->name1 : '',
                            'name2' => $vendor ? $vendor->name2 : '',
                            'campaign_year_id' => $campaign_year ? $campaign_year->id : null,
                            'emplid' => $row->emplid,
                            'GUID' =>  $row->GUID, 
                            'frequency' => $row->frequency,
                            'pledge' => $row->pledge,
                            'percent' => $row->percent,
                            'amount' => $row->amount,
                        ]);
                    }
                }
            } else {
                $this->info( $response->status() );
                $this->info( $response->body() );
            }

        }
    }



}
