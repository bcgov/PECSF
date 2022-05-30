<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PledgeHistory;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportPledgeHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importPledgeHistory';

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

        $this->info("Update/Create - Pledge History");
        $this->UpdatePledgeHistory();

        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();

        return 0;

    }

    protected function UpdatePledgeHistory() 
    {
        // Truncate Pledge History table
        PledgeHistory::truncate();

        // Loading pledge history data
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

        if ($response->successful()) {
            $data = json_decode($response->body())->value; 
            $batches = array_chunk($data, 1000);
            
            foreach ($batches as $key => $batch) {
                $this->info( $key . ' - ' . count($batch) );
                foreach ($batch as $row) {

                    // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                    $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first(); 
                    $charity = \App\Models\charity::where('registration_number', $row->charity_bn)->first();
                    $campaign_year = \App\Models\CampaignYear::where('calendar_year', $row->yearcd)->first();

                    PledgeHistory::Create([
                        'campaign_type' => $row->campaign_type,
                        'source' => $row->source,
                        'tgb_reg_district' => $row->tgb_reg_district,
                        'region_id' => $region ? $region->id : null,
                        'charity_bn' => $row->charity_bn,
                        'charity_id' => $charity ? $charity->id : null,
                        'yearcd' => $row->yearcd,
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
