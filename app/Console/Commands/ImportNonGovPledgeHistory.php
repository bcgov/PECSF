<?php

namespace App\Console\Commands;

use stdClass;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use App\Models\NonGovPledgeHistory;
use Illuminate\Support\Facades\Http;
use App\Models\NonGovPledgeHistorySummary;

class ImportNonGovPledgeHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportNonGovPledgeHistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the Non Gov Pledge History data from BI';

        /* Variable for logging */
        protected $message;
        protected $status;
        protected $row_count;

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

        $this->row_count = 0;
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

        if ($last_task) {
            $start_year = (now()->month <= 3) ? now()->year - 2 : now()->year - 1;
        }


        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);
        
        $this->LogMessage( now() );    
        $this->LogMessage("Step - 1 : Create - Non-Gov Pledge History");
        for ($yr = $start_year; $yr <= now()->year; $yr++) {
            $this->UpdateNonGovPledgeHistory($yr);
        }

        $this->LogMessage( now() );    
        $this->LogMessage("Step - 2 : Create - Non-Gov Pledge History Summary");
        $this->UpdatePledgeHistorySummary();

        $this->LogMessage( now() );    

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
    
    }

    protected function UpdateNonGovPledgeHistory($in_year) 
    {

        // Clear Up Pledge History table
        NonGovPledgeHistory::where('yearcd', $in_year)->delete();

        $this->LogMessage( 'Loading pledge history data for '. $in_year);
        $filter = '(yearcd eq '. $in_year .')';
                
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_NON_GOV_PLEDGE_HISTORY_BI_ENDPOINT') .'?$count=true&$top=1&$filter='.$filter);

            $row_count = json_decode($response->body())->{'@odata.count'};
            
            $size = 10000;
            for ($i = 0; $i <= $row_count / $size ; $i++) {

                $top  = $size;
                $skip = $size * $i;
                // Loading pledge history data
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                    ->get(env('ODS_INBOUND_REPORT_NON_GOV_PLEDGE_HISTORY_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip.'&$filter='.$filter) ;

                $this->LogMessage( '  Total Count ='. $row_count .' $i ='. $i .' $top ='. $top .' $skip '. $skip .' $filter ='. $filter);
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

         
                            // // Skip if the GUID is blank
                            // if (empty($row->GUID)) {
                            //     continue;
                            // }

                            NonGovPledgeHistory::Create([
                                'pledge_type' => $row->campaign_type,
                                'source' => $row->source,
                                'tgb_reg_district' => $row->tgb_reg_district,
                                'charity_bn' => $row->charity_bn,
                                'yearcd' => $row->yearcd,
                                'org_code' => $row->tgb_org_cde,
                                'emplid' => $row->emplid , // $row->tgb_org_cde == 'GOV' ? $row->emplid : null,
                                'pecsf_id' => $row->tgb_pecsf_id, // $row->tgb_org_cde <> 'GOV' ? $row->tgb_pecsf_id : null, 
                                'name' => $row->employee_disp_name,
                                'first_name' => $row->first_name,
                                'last_name' => $row->last_name,
                                'guid' =>  $row->GUID, 
                                'vendor_id' => $row->vendor_id,
                                'additional_info' => $row->additional_info,
                                'frequency' => $row->frequency,
                                'per_pay_amt' => $row->per_pay_amt,
                                'pledge' => $row->pledge,
                                'percent' => $row->percent,
                                'amount' => $row->amount,
                                'deduction_code' => $row->deduction_code,

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
        NonGovPledgeHistorySummary::truncate();

        DB::statement( $this->getInsertAnnualSummarySQL() );
        DB::statement( $this->getInsertDonateNowSummarySQL() );

    }

    private function getInsertAnnualSummarySQL(): string
    {
        return <<<SQL
            insert into non_gov_pledge_history_summaries
                (pledge_history_id, org_code, emplid, pecsf_id, yearcd, source, pledge_type, frequency, per_pay_amt,pledge, region, event_type, event_sub_type, event_deposit_date)
                select min(non_gov_pledge_histories.id), org_code, emplid, pecsf_id, yearcd, case when max(source) = 'Pool' then 'P' else 'C' end,  
                    pledge_type, frequency, 
                    case when frequency = 'Bi-Weekly' then max(pledge / 26) else 0 end per_pay_amt, 
                    max(pledge) as pledge,
                    case when source = 'Pool' then (select regions.name from regions where non_gov_pledge_histories.tgb_reg_district  = regions.code) else '' end,
                    event_type, event_sub_type, event_deposit_date
                from non_gov_pledge_histories  
                 where pledge_type in ('Annual', 'Event') 
                group by org_code, emplid, pecsf_id, yearcd, pledge_type, frequency, event_type, event_sub_type, event_deposit_date;
        SQL;

    }

    private function getInsertDonateNowSummarySQL(): string
    {
        // Donate Now always Non-Pool and single charity
        return <<<SQL
            insert into non_gov_pledge_history_summaries
                (pledge_history_id, org_code, emplid, pecsf_id, yearcd, source, pledge_type, frequency, per_pay_amt,pledge, region, event_type, event_sub_type, event_deposit_date)
                select non_gov_pledge_histories.id, org_code, emplid, pecsf_id, yearcd, case when max(source) = 'Pool' then 'P' else 'C' end,  
                    pledge_type, frequency,  
                    case when frequency = 'Bi-Weekly' then max(pledge / 26) else 0 end per_pay_amt, 
                    max(pledge) as pledge,
                    case when source = 'Pool' then (select regions.name from regions where non_gov_pledge_histories.tgb_reg_district  = regions.code) else '' end,
                    event_type, event_sub_type, event_deposit_date
                from non_gov_pledge_histories  
                 where pledge_type in ('Donate Today');
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
