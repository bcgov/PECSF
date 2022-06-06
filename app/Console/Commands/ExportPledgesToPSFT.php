<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Pledge;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ExportPledgesToPSFT extends Command
{

    protected $success;
    protected $failure;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ExportPledgesToPSFT'; 

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending pledge transactions to PeopleSoft via ODS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->success = 0;
        $this->failure = 0;

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

        // Step 1 : Send Campiagn Type data to PeopleSoft access endpoint 
        $this->info("Sending Annual Type pledge data to PeopleSoft");
        $this->sendCampaignDonationToPeopleSoft();
        
        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();

            
    }

    private function sendCampaignDonationToPeopleSoft() {

        $pledgeData = Pledge::where('ods_export_status', null)->orderBy('updated_at')->get();
        foreach($pledgeData as $pledge) {

            // switch ($pledge->frequency)
            // {
            //     case 'one time':
            //         $Donation_Type = "A";   // Annual 
            //         $Yearcd = 
            //         $Deduction_Code = "PECSF1";
            //         $start_date = $pledge->updated_at->format('Y-m-d');
            //         $end_date = $pledge->updated_at->format('Y-m-d');
            //         $amount = $pledge->amount;
            //         break;
            //     case 'bi-weekly':
            //         $Donation_Type = "B";
            //         $Deduction_Code = "PECSF";
            //         $start_date = $pledge->updated_at->format('Y-m-d');
            //         $end_date = $pledge->updated_at->format('Y-12-31');
            //         $amount = $pledge->amount;
            //         break;
            //     default:
            //         break;                    
            // }


            $one_time_sent = false;
            $pay_period_sent = false;

            // One-time

            if ($pledge->one_time_amount != 0) { 
                $one_time_data = [
                    "@odata.type" => "CDataAPI.[employee_info]",
                    "date_posted" =>  Carbon::now()->format('Y-m-d'), // Carbon::now()->format('c'), 

                    "Donation_Type" => 'A',   // Always 'A" for Annual pledge
                    'yearcd' => $pledge->campaign_year->calendar_year,
                    'transaction_id' => $pledge->id,
                    'frequency' => 'One-Time',

                    "GUID" => $pledge->user->guid,
                    "Employee_Name" => $pledge->user->name,    
                    "Amount" =>  $pledge->one_time_amount,

                    "Deduction_Code" => "PECSF1",   // always "PECSF1" for one-time deduction
                    "pledge_start_date" => $pledge->updated_at->format('Y-m-d'),
                    "pledge_end_date" => $pledge->updated_at->format('Y-m-d'),
                ];

                $one_time_sent = $this->sendData($one_time_data);
            } else {
                $one_time_sent = true;
            }

            // Pay period pledge
            if ($pledge->pay_period_amount != 0) { 
                $pay_period_data = [
                    "@odata.type" => "CDataAPI.[employee_info]",
                    "date_posted" =>  Carbon::now()->format('Y-m-d'), // Carbon::now()->format('c'), 

                    "donation_Type" => 'A',   // Always 'A" for Annual pledge
                    'yearcd' => $pledge->campaign_year->calendar_year,
                    'transaction_id' => $pledge->id,
                    'frequency' => 'Bi-Weekly', 

                    "GUID" => $pledge->user->guid,
                    "Employee_Name" => $pledge->user->name,    
                    "Amount" =>  $pledge->pay_period_amount,

                    "Deduction_Code" => "PECSF",   // always "PECSF" for one-time deduction
                    "pledge_start_date" => $pledge->updated_at->format('Y-m-d'),
                    "pledge_end_date" => $pledge->updated_at->format('Y-12-31'),    // always last date of the year
                ];
                $pay_period_sent = $this->sendData($pay_period_data);
            } else {
                $pay_period_sent = true;
            }                
                       
            // Update the complete flag in pledge table
            if ($one_time_sent && $pay_period_sent) {
                $pledge->ods_export_status = 'C';
                $pledge->ods_export_at = Carbon::now()->format('c');
                $pledge->save();
            }

            // //$response = $this->pushToODS($pushData);
            // $response = Http::withBasicAuth(
            //     env('ODS_USERNAME'),
            //     env('ODS_TOKEN')
            //     // config('services.ods.username'),
            //     // config('services.ods.token')
            // )->post( env('ODS_OUTBOUND_PLEDGE_PSFT_ENDPOINT') , $pushData);

            // if ($response->successful()) {
            //     $pledge->ods_export_status = 'C';
            //     $pledge->ods_export_at = Carbon::now()->format('c');
            //     $pledge->save();
            //     $success += 1;
            // } else {
            //     $failure += 1;
            // }

        }

        $this->info("Sent data complete");
        $this->info("Success - " . $this->success);
        $this->info("failure - " . $this->failure);
        return 0;

    }

    protected function sendData($pushdata) {

        $response = Http::withBasicAuth(
            env('ODS_USERNAME'),
            env('ODS_TOKEN')
        )->withBody( json_encode($pushdata), 'application/json')
        ->post( env('ODS_OUTBOUND_PLEDGE_PSFT_ENDPOINT') );

        if ($response->successful()) {
            $this->success += 1;
            return true;

        } else {
            $this->info( $response->status() );
            $this->info( $response->body() );
            $this->failure += 1;

            return false;
        }
      
    }


    // private function cleanUpODS() {

    //     Pledge::where('ods_export_status','C')->update(['ods_export_status' => '', 'ods_export_at' => null]);
        
    //     $response = Http::withBasicAuth(
    //         env('ODS_USERNAME'),
    //         env('ODS_TOKEN')
    //     )->get( env('ODS_OUTBOUND_PLEDGE_PSFT_ENDPOINT') );

    //     $responseBody = json_decode($response->getBody(), true);
    //     $pledgeData = $responseBody['value'];

    //     foreach($pledgeData as $item)
    //     {

    //         $guid = $item['GUID'];
    //         $date_posted = $item['date_posted'];
    //         $Donation_Type = $item['Donation_Type'];

    //         if ($response->successful() ) {

    //             if ($date_posted >= date('2022-02-15')) {
    //                 $parms = "(Donation_Type='$Donation_Type',GUID='$guid',date_posted='$date_posted')";
    //                 $response = Http::withBasicAuth(
    //                     env('ODS_USERNAME'),
    //                     env('ODS_TOKEN')
    //                 )->delete( env('ODS_OUTBOUND_PLEDGE_PSFT_ENDPOINT').$parms);

    //                 if ($response->failed() ) {
    //                     $this->info('failure');
    //                 }
    //             }
    //         } else {
    //             $this->info('failure');
    //         }

    //     }
        
    // } 

}
