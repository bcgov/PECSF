<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PledgeHistory;
use Illuminate\Console\Command;
// use App\Models\RegionalDistrict;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistoryVendor;
use App\Models\PledgeHistorySummary;
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

    /* Variable for logging */
    protected $message;
    protected $status;
    protected $reload_start_year;
    

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->message = '';
        $this->status = 'Completed';
   
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Determine the start year
        $last_task = ScheduleJobAudit::where('job_name', $this->signature)
                            ->where('status', 'Completed')
                            ->first();

        $start_year = 2005;
        if ($last_task) {
            $start_year = (now()->month <= 3) ? now()->year - 2 : now()->year - 1;
        }

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        // $this->LogMessage( now() );
        // $this->LogMessage("Step - 1 : Update/Create - Region District");
        // $this->UpdateRegionalDistrict();
        
        $this->LogMessage( now() );
        $this->LogMessage("Step - 1 : Reload - Pledge History Vendor");
        $this->UpdatePledgeHistoryVendor();

        $this->LogMessage( now() );    
        $this->LogMessage("Step - 2 : Reload - Pledge History");
        for ($yr = $start_year; $yr <= now()->year; $yr++) {
            $this->UpdatePledgeHistory($yr);
        }
        $this->LogMessage( now() );    
        $this->LogMessage("Step - 3 : Reload - Pledge History Summary");
        $this->UpdatePledgeHistorySummary();

        $this->LogMessage( now() );    

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;

    }


    // protected function UpdateRegionalDistrict()
    // {

    //     $count = 0;

    //     try {

    //         $response = Http::withHeaders(['Content-Type' => 'application/json'])
    //             ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
    //             ->get(env('ODS_INBOUND_REPORT_REGIONAL_DISTRICTS_BI_ENDPOINT'));

    //         if ($response->successful()) {
    //             $data = json_decode($response->body())->value;
    //             $batches = array_chunk($data, 1000);

    //             foreach ($batches as $batch) {

    //                 foreach ($batch as $row) {

    //                     RegionalDistrict::updateOrCreate([
    //                         'tgb_reg_district' => $row->tgb_reg_district,

    //                     ], [
    //                         'reg_district_desc' => $row->reg_district_desc,
    //                         'development_region' => $row->development_region,
    //                         'provincial_quadrant' => $row->provincial_quadrant,
    //                     ]);

    //                     $count += 1;
    //                 }
    //             }

    //             $this->LogMessage ('Total rows : ' . $count );

    //         } else {

    //             $this->status = 'Error';
    //             $this->LogMessage( $response->status() . ' - ' . $response->body() );

    //         }

    //     } catch (\Exception $ex) {

    //         $this->status = 'Error';
    //         $this->LogMessage( $ex->getMessage() );

    //         return 1;

    //     }
        
    // }

    protected function UpdatePledgeHistoryVendor() 
    {

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_VNDR_BI_ENDPOINT') .'?$count=true&$top=1');

            $row_count = json_decode($response->body())->{'@odata.count'};
           
            if ($row_count > 0) {
                // Truncate Pledge History table when records returned from BI
                PledgeHistoryVendor::truncate();
            }

            $size = 10000;
            for ($i = 0; $i <= $row_count / $size ; $i++) {

                $top  = $size;
                $skip = $size * $i;
                // Loading pledge history data
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                    ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_VNDR_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip) ;

                $this->LogMessage( 'Total Count ='. $row_count .' $i ='. $i .' $top ='. $top .' $skip '. $skip);
                // Loading pledge history data
                // $response = Http::withHeaders(['Content-Type' => 'application/json'])
                //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

                if ($response->successful()) {
                    $data = json_decode($response->body())->value; 
                    $batches = array_chunk($data, 1000);
                    
                    foreach ($batches as $key => $batch) {
                        $this->LogMessage( '    -- each batch (1000) $key - '. $key );
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

                    $this->LogMessage('Total rows : ' . $row_count );

                } else {

                    $this->status = 'Error';
                    $this->LogMessage( $response->status() . ' - ' . $response->body() );

                }

            }

        } catch (\Exception $ex) {

            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;

        }
    }


    protected function UpdatePledgeHistory($in_year) 
    {
        // Clear Up Pledge History table
        PledgeHistory::where('yearcd', $in_year)->delete();

        $this->LogMessage( 'Loading pledge history data for '. $in_year);
        $filter = '(yearcd eq '. $in_year .')';

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT') .'?$count=true&$top=1&$filter='.$filter);

            $row_count = json_decode($response->body())->{'@odata.count'};
            
            $size = 10000;
            for ($i = 0; $i <= $row_count / $size ; $i++) {

                $top  = $size;
                $skip = $size * $i;
                // Loading pledge history data
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                    ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip.'&$filter='.$filter) ;

                $this->LogMessage( '  Total Count ='. $row_count .' $i ='. $i .' $top ='. $top .' $skip ='. $skip .' $filter ='. $filter);
                // Loading pledge history data
                // $response = Http::withHeaders(['Content-Type' => 'application/json'])
                //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

                if ($response->successful()) {
                    $data = json_decode($response->body())->value; 
                    $batches = array_chunk($data, 1000);
                    
                    foreach ($batches as $key => $batch) {
                        $this->LogMessage( '    -- each batch (1000) $key - '. $key );
                        foreach ($batch as $row) {

                            // Skip if the GUID is blank
                            // if (empty($row->GUID)) {
                            //     continue;
                            // }

                            // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                            $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first(); 
                            $charity = \App\Models\Charity::where('registration_number', $row->charity_bn)->first();
                            $campaign_year = \App\Models\CampaignYear::where('calendar_year', $row->yearcd)->first();

                            $vendor = PledgeHistoryVendor::where('tgb_reg_district', $row->tgb_reg_district)
                                        ->where('charity_bn',  $row->charity_bn)
                                        ->where('effdt', function($query) use($row) {
                                            return $query->selectRaw('max(effdt)')
                                                    ->from('pledge_history_vendors as A')
                                                    ->whereColumn('A.charity_bn', 'pledge_history_vendors.charity_bn')
                                                    ->whereColumn('A.tgb_reg_district', 'pledge_history_vendors.tgb_reg_district')
                                                    ->whereColumn('A.vendor_id', 'pledge_history_vendors.vendor_id')
                                                    ->whereColumn('A.yearcd', 'pledge_history_vendors.yearcd')
                                                    ->where('A.vendor_id', '=', $row->vendor_id)
                                                    ->where('A.yearcd', $row->yearcd);
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
                                'per_pay_amt' => $row->per_pay_amt,
                                'pledge' => $row->pledge,
                                'percent' => $row->percent,
                                'amount' => $row->amount,
                                'vendor_id' => $row->vendor_id,
                                'additional_info' => $row->additional_info,

                                'vendor_name1' => $row->vendor_name1,
                                'vendor_name2' => $row->vendor_name2,
                                'vendor_bn' => $row->vendor_bn,
                                'remit_vendor' => $row->remit_vendor,
                                'deptid' => $row->DEPTID,
                                'city' => $row->city,

                                'business_unit' => $row->business_unit,
                                'event_descr' => $row->act_descr,         // Event's description e.g. cheque#
                                'event_type' => $row->deduction_code,     // For Event, is donation type 
                                'event_sub_type'=> $row->sub_type,        // For Event, is donation sub type 
                                'event_deposit_date' => $row->act_date,       // Event's deposit date  

                                'created_date' => $row->created,

                            ]);
                        }
                    }

                    $this->message .= 'Total rows : ' . $row_count . PHP_EOL;

                } else {

                    $this->status = 'Error';
                    $this->LogMessage( $response->status() . ' - ' . $response->body() );

                }

            }

        } catch (\Exception $ex) {
        
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;

        }
    }

    protected function UpdatePledgeHistorySummary() {

        // Truncate Pledge History table
        PledgeHistorySummary::truncate();

        DB::statement( $this->getInsertAnnualSummarySQL() );
        DB::statement( $this->getInsertEventSummarySQL() );
        DB::statement( $this->getInsertDonateNowSummarySQL() );

    }

    private function getInsertAnnualSummarySQL(): string
    {
        return <<<SQL
            insert into pledge_history_summaries
                (pledge_history_id,emplid,yearcd,source,campaign_type,frequency,per_pay_amt,pledge,region, event_type, event_sub_type, event_deposit_date)
                select min(pledge_histories.id), emplid, yearcd, case when max(source) = 'Pool' then 'P' else 'C' end,  
                    campaign_type, frequency, 
                    case when frequency = 'Bi-Weekly' then max(pledge / 26) else 0 end per_pay_amt, max(pledge) as pledge, 
                    case when max(source) = 'Pool' then max(pledge_histories.tgb_reg_district) else '' end,

                    null as event_type,
                    null as event_sub_type,
                    null as event_deposit_date

                from pledge_histories  
                where campaign_type in ('Annual') 
                group by emplid, yearcd, campaign_type, frequency;
        SQL;

    }

    private function getInsertEventSummarySQL(): string
    {
        // Donate Now always Non-Pool and single charity
        return <<<SQL

            insert into pledge_history_summaries               
                (pledge_history_id,emplid,yearcd,source,campaign_type,frequency,per_pay_amt,pledge,region, event_type, event_sub_type, event_deposit_date)
                select min(pledge_histories.id), emplid, yearcd, case when max(source) = 'Pool' then 'P' else 'C' end,
                        campaign_type, frequency, max(per_pay_amt), max(pledge),    
                        case when max(source) = 'Pool' then max(pledge_histories.tgb_reg_district) else '' end,
                        max(event_type) as event_type,
                        max(event_sub_type) as event_sub_type,
                        max(event_deposit_date) as event_deposit_date
                    from pledge_histories 
                where campaign_type in ('Event')
                group by emplid, yearcd, campaign_type, frequency;

        SQL;

    }

    private function getInsertDonateNowSummarySQL(): string
    {
        // Donate Now always Non-Pool and single charity
        return <<<SQL
            insert into pledge_history_summaries               
                (pledge_history_id,emplid,yearcd,source,campaign_type,frequency,per_pay_amt,pledge,region, event_type, event_sub_type, event_deposit_date)
                select pledge_histories.id, emplid, yearcd, case when source = 'Pool' then 'P' else 'C' end,
                        campaign_type, frequency, per_pay_amt, pledge,    
                        case when source = 'Pool' then pledge_histories.tgb_reg_district else '' end,
                        null as event_type,
                        null as event_sub_type,
                        null as event_deposit_date
                    from pledge_histories 
                where campaign_type in ('Donate Today');
        SQL;

    }




    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        $this->task->save();
        
    }

}
