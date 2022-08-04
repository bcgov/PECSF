<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
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

    /* Source Type is HCM */
    protected $message;
    protected $status;
    

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();


        $this->success = 0;
        $this->skip = 0;
        $this->failure = 0;

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

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        // Step 1 : Send Campiagn Type data to PeopleSoft access endpoint 
        $this->LogMessage( now() );        
        $this->LogMessage("Sending Annual Campaign Type pledge data to PeopleSoft");
        $this->sendCampaignDonationToPeopleSoft();
        
        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();


            
    }

    private function sendCampaignDonationToPeopleSoft() {

        $pledgeData = Pledge::join('organizations', 'pledges.organization_id', 'organizations.id')
                            ->where('organizations.code', 'GOV')
                            ->where('pledges.ods_export_status', null)
                            ->select('pledges.*')
                            ->orderBy('pledges.id')->get();

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

            // validation -- GUID 
            $user = User::where('id', $pledge->user_id)->first();
            if (!$user->guid ) {
                $this->LogMessage( "(SKIP) No GUID found in Transaction {$pledge->id} - " . json_encode( $pledge ) );
                $this->skip += 1;
                continue;
            }


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
                    // "pledge_start_date" => $pledge->updated_at->format('Y-m-d'),
                    // "pledge_end_date" => $pledge->updated_at->format('Y-m-d'),
                    'pledge_start_date' => $pledge->campaign_year->calendar_year . '-01-01',
                    'pledge_end_date' => $pledge->campaign_year->calendar_year . '-12-31',
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
                    // "pledge_start_date" => $pledge->updated_at->format('Y-m-d'),
                    // "pledge_end_date" => $pledge->updated_at->format('Y-12-31'),    // always last date of the year
                    'pledge_start_date' => $pledge->campaign_year->calendar_year . '-01-01',
                    'pledge_end_date' => $pledge->campaign_year->calendar_year . '-12-31',
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

                //                         
                $this->LogMessage( "    Transaction {$pledge->id} has been sent - " . json_encode( $pledge ) );

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


        $this->LogMessage("Sent data was completed");
        $this->LogMessage("Success - " . $this->success);
        $this->LogMessage("Skip    - " . $this->skip);
        $this->LogMessage("failure - " . $this->failure);
        $this->LogMessage( now() );
        
        return 0;
    }

    protected function sendData($pushdata) {

        try {

            $response = Http::withBasicAuth(
                env('ODS_USERNAME'),
                env('ODS_TOKEN')
            )->withBody( json_encode($pushdata), 'application/json')
            ->post( env('ODS_OUTBOUND_PLEDGE_PSFT_ENDPOINT') );

            if ($response->successful()) {
                $this->success += 1;
                return true;

            } else {

                // log message in system
                $this->status = 'Error';
                $this->LogMessage( "(Error) - Data - " . json_encode($pushdata) );
                $this->LogMessage( "        - " . $response->status() . ' - ' . $response->body() );

                $this->failure += 1;

                return false;
            }

        } catch (\Exception $ex) {

            // log message in system
            $this->status = 'Error';
            $this->LogMessage( "(Error) - " . json_encode($pushdata) );
            $this->LogMessage( "          " - $ex->getMessage() );
            
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
