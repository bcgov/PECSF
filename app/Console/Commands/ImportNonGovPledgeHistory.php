<?php

namespace App\Console\Commands;

use stdClass;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use App\Models\NonGovPledgeHistory;
use Illuminate\Support\Facades\Http;

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

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);
        
        $this->LogMessage( now() );    
        $this->LogMessage("Step - 1 : Create - Non-Gov Pledge History");
        $this->UpdateNonGovPledgeHistory();

        $this->LogMessage( now() );    

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
    
    }

    protected function UpdateNonGovPledgeHistory() {

        // Truncate Pledge History table
        NonGovPledgeHistory::truncate();

        $currentYear = Carbon::now()->format('Y');

        // Loop the years
        for($yr=2005; $yr <= $currentYear; $yr++) {
            
            $this->row_count = 0;

            $this->UpdateNonGovPledgeHistoryForYear( $yr );

            $this->LogMessage ( 'Total rows for (' . $yr . ') : ' . $this->row_count );
        }

        
    }

    protected function UpdateNonGovPledgeHistoryForYear( $year ) 
    {

        $pushdata = new stdClass();
        $pushdata->YearCd = $year;

        try {
            // $response = Http::withHeaders(['Content-Type' => 'application/json'])
            //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            //     ->get(env('ODS_INBOUND_REPORT_NON_GOV_PLEDGE_HISTORY_BI_ENDPOINT') .'?$count=true&$top=1');

            $response = Http::withBasicAuth(
                env('ODS_USERNAME'),
                env('ODS_TOKEN')
            )->withBody( json_encode($pushdata), 'application/json')
            ->post( env('ODS_INBOUND_REPORT_NON_GOV_PLEDGE_HISTORY_BI_ENDPOINT') );

            // $row_count = json_decode($response->body())->{'@odata.count'};
            
                if ($response->successful()) {
                    $data = json_decode($response->body())->value; 
                    
                    
                    foreach ($data as $row) {

                        $this->row_count += 1;

                        $first_name = null;
                        $last_name = null;

                        if (isset($row->name)) {
                            $names = explode(",", $row->name);
                            if (count($names) == 2) {
                                $last_name = $names[0];
                                $first_name = $names[1];
                            }
                        }

                        
                        if (isset($row->tgb_pecsf_id)) {

                            NonGovPledgeHistory::Create([
                                'org_code' => $row->tgb_org_cde,
                                'yearcd' => $row->yearcd,
                                'pledge_type' => $row->pledge_type,
                                'emplid' => $row->emplid,
                                'pecsf_id' => $row->tgb_pecsf_id,
                                'vendor_id' => $row->vendor_id,
                                'vendor_bn' => $row->vendor_bn,
                                'remit_vendor' => $row->remit_vendor,
                                'remit_vendor_bn' => $row->remit_vendor_bn,
                                'name' => $row->name,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'city' => $row->city,
                                'amount' => $row->amount,
                            ]);
                        }
                    }

                } else {

                    $this->status = 'Error';
                    $this->LogMessage( $response->status() . ' - ' . $response->body() );

                }


        } catch (\Exception $ex) {
        
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;

        }
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
