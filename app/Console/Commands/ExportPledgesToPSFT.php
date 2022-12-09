<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pledge;
use App\Models\PayCalendar;
use App\Models\CampaignYear;
use App\Models\ExportAuditLog;
use App\Models\DonateNowPledge;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;
use App\Models\SpecialCampaignPledge;

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

        $this->row_count = 0;
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
        $this->LogMessage("1) Sending Annual Campaign Type pledge data to PeopleSoft");
        $this->sendCampaignDonationToPeopleSoft();
        
        // Step 2 : Send Donate Now Pledge data to PeopleSoft access endpoint 
        $this->LogMessage( "" );        
        $this->LogMessage("2) Sending Donate Now Type pledge data to PeopleSoft");
        $this->sendDonateNowToPeopleSoft();

        // Step 3 : Send Special Campaign Pledge data to PeopleSoft access endpoint 
        $this->LogMessage( "" );        
        $this->LogMessage("3) Sending Special Campaign Type pledge data to PeopleSoft");
        $this->sendSpecialCampaignToPeopleSoft();

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();


            
    }

    private function sendCampaignDonationToPeopleSoft() {

        $this->row_count = 0;
        $this->success = 0;
        $this->skip = 0;
        $this->failure = 0;

        $pledge_count = 0;
        $pledge_skip_count = 0;
        $pledge_success_count = 0;

        $pledgeData = Pledge::join('organizations', 'pledges.organization_id', 'organizations.id')
                            ->where('organizations.code', 'GOV')
                            ->where('pledges.ods_export_status', null)
                            ->select('pledges.*')
                            ->orderBy('pledges.id')->get();

        foreach($pledgeData as $pledge) {

            $pledge_count += 1;

            // validation -- GUID 
            $user = User::where('id', $pledge->user_id)->first();
            if (!$user->guid ) {
                $this->LogMessage( "(SKIP) No GUID found in Transaction {$pledge->id} - " . json_encode( $pledge->only(['id','organization_id','user_id','campaign_year_id', 'type', 'f_s_pool_id','one_time_amount','pay_period_amount','goal_amount','ods_export_status','ods_export_at'])) );
                $pledge_skip_count += 1;
                continue;
            }

            if ($user->source_type == 'LCL') {
                $this->LogMessage( "(SKIP) The user of this transaction is {$user->source_type} - " . json_encode( $pledge->only(['id','organization_id','user_id','campaign_year_id', 'type', 'f_s_pool_id','one_time_amount','pay_period_amount','goal_amount','ods_export_status','ods_export_at'])) );
                $pledge_skip_count += 1;
                continue;
            }

            // check whether the campaign is not open or active
            $campaign_year = CampaignYear::where('id',   $pledge->campaign_year_id )->first();
            if (!($campaign_year->canSendToPSFT() )) {
                $this->LogMessage( "(SKIP) Campaign Year is not allow to send to PSFT in Transaction {$pledge->id} - " . json_encode( $pledge->only(['id','organization_id','user_id','campaign_year_id', 'type', 'f_s_pool_id','one_time_amount','pay_period_amount','goal_amount','ods_export_status','ods_export_at'])) );
                $pledge_skip_count += 1;
                continue;
            }

            // Calculate the deduct pay from 
            $check_dt = $pledge->campaign_year->calendar_year . '-01-01';
            $period = PayCalendar::where('check_dt', '>=',  $check_dt)->orderBy('check_dt')->first();
            $start_date = $period ? $period->check_dt : $check_dt;
            $end_date   = $pledge->campaign_year->calendar_year . '-12-31';


            $one_time_sent = false;
            $pay_period_sent = false;

            // One-time

            if ($pledge->one_time_amount != 0) { 
                $this->row_count += 1;

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
                    'pledge_start_date' => $start_date,  // $pledge->campaign_year->calendar_year . '-01-01',
                    'pledge_end_date' => $end_date,    // $pledge->campaign_year->calendar_year . '-12-31',
                ];

                $one_time_sent = $this->sendData($one_time_data);

                if ($one_time_sent) { 
                    $this->LogMessage( "    Transaction {$pledge->id} (One-Time) has been sent - " . json_encode( $one_time_data ) );
                }

            } else {
                $one_time_sent = true;
            }

            // Pay period pledge
            if ($pledge->pay_period_amount != 0) { 
                $this->row_count += 1;

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
                    'pledge_start_date' => $start_date, // $pledge->campaign_year->calendar_year . '-01-01',
                    'pledge_end_date' => $end_date,     // $pledge->campaign_year->calendar_year . '-12-31',
                ];
                $pay_period_sent = $this->sendData($pay_period_data);

                if ($pay_period_sent) { 
                    $this->LogMessage( "    Transaction {$pledge->id} (Bi-Weekly) has been sent - " . json_encode( $pay_period_data ) );
                }

            } else {
                $pay_period_sent = true;
            }                
                       
            // Update the complete flag in pledge table
            if ($one_time_sent && $pay_period_sent) {

                $pledge_success_count += 1;

                ExportAuditLog::create([
                    'schedule_job_name' => $this->task->job_name,
                    'schedule_job_id' => $this->task->id,
                    'to_application' => 'PSFT',
                    'table_name' => 'pledges',
                    'row_id' => $pledge->id,
                    'row_values' => json_encode($pledge),
                ]);

                $pledge->ods_export_status = 'C';
                $pledge->ods_export_at = Carbon::now()->format('c');
                $pledge->save();

                $this->LogMessage( "    Transaction {$pledge->id} has been sent - " . json_encode( $pledge ) );

            }

        }


        $this->LogMessage("Sent data was completed");
        $this->LogMessage("Total number of pledge processed - " . $pledge_count);
        $this->LogMessage("Total number of pledge skipped   - " . $pledge_skip_count);
        $this->LogMessage("Total number of pledge sent      - " . $pledge_success_count);

        $this->logMessage("");
        $this->LogMessage("ODS Processed - " . $this->row_count);
        $this->LogMessage("ODS Success   - " . $this->success);
        $this->LogMessage("ODS failure   - " . $this->failure);
        $this->LogMessage( now() );
        
        return 0;
    }


    private function sendDonateNowToPeopleSoft() {

        $this->row_count = 0;
        $this->success = 0;
        $this->skip = 0;
        $this->failure = 0;


        $pledgeData = DonateNowPledge::join('organizations', 'donate_now_pledges.organization_id', 'organizations.id')
                            ->where('organizations.code', 'GOV')
                            ->where('donate_now_pledges.ods_export_status', null)
                            ->where('donate_now_pledges.cancelled', null)
                            ->select('donate_now_pledges.*')
                            ->orderBy('donate_now_pledges.id')->get();

        foreach($pledgeData as $pledge) {

            $this->row_count += 1;

            if (!($pledge->canSendToPSFT())) {
                
                $this->LogMessage( "(SKIP) date to send is not reached - " . json_encode( $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at'])) );
                $this->skip += 1;
                continue;
            }

            // validation -- GUID 
            $user = User::where('id', $pledge->user_id)->first();
            if (!$user->guid ) {
                $this->LogMessage( "(SKIP) No GUID found in Transaction {$pledge->id} - " . $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at']) );
                $this->skip += 1;
                continue;
            }

            // One-time
            if ($pledge->one_time_amount != 0) { 
                $one_time_data = [
                    "@odata.type" => "CDataAPI.[employee_info]",
                    "date_posted" =>  Carbon::now()->format('Y-m-d'), // Carbon::now()->format('c'), 

                    "Donation_Type" => 'N',   // Always 'N" for Donate Now
                    'yearcd' => $pledge->yearcd,
                    'transaction_id' => $pledge->id,
                    'frequency' => 'One-Time',

                    "GUID" => $pledge->user->guid,
                    "Employee_Name" => $pledge->user->name,    
                    "Amount" =>  $pledge->one_time_amount,

                    "Deduction_Code" => "PECADD",   // always "PECADD" for one-time Donate Now deduction
                  
                    'pledge_start_date' => $pledge->deduct_pay_from,
                    'pledge_end_date' => $pledge->deduct_pay_from,
                ];

                $result = $this->sendData($one_time_data);

                if ($result) {
                
                    ExportAuditLog::create([
                        'schedule_job_name' => $this->task->job_name,
                        'schedule_job_id' => $this->task->id,
                        'to_application' => 'PSFT',
                        'table_name' => 'donate_now_pledges',
                        'row_id' => $pledge->id,
                        'row_values' => json_encode($pledge),
                    ]);
                    
                    // Update the complete flag in pledge table
                    $pledge->ods_export_status = 'C';
                    $pledge->ods_export_at = Carbon::now()->format('c');
                    $pledge->save();

                    // Log Message                 
                    $this->LogMessage( "    Transaction {$pledge->id} has been sent - " . $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at']) );
                }
            }
        }


        $this->LogMessage("Sent data was completed");
        $this->LogMessage("ODS Processed - " . $this->row_count);
        $this->LogMessage("ODS Skip      - " . $this->skip);
        $this->LogMessage("ODS Success   - " . $this->success);
        $this->LogMessage("ODS failure   - " . $this->failure);
        $this->LogMessage( now() );
        
        return 0;
    }


    private function sendSpecialCampaignToPeopleSoft() {

        $this->row_count = 0;
        $this->success = 0;
        $this->skip = 0;
        $this->failure = 0;

        $pledgeData = SpecialCampaignPledge::join('organizations', 'special_campaign_pledges.organization_id', 'organizations.id')
                            ->where('organizations.code', 'GOV')
                            ->where('special_campaign_pledges.ods_export_status', null)
                            ->select('special_campaign_pledges.*')
                            ->orderBy('special_campaign_pledges.id')
                            ->get();

        foreach($pledgeData as $pledge) {
            
            $this->row_count += 1;

            if (!($pledge->canSendToPSFT())) {
                $this->LogMessage( "(SKIP) date to send is not reached - " . json_encode( $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at'])) );
                $this->skip += 1;
                continue;
            }

            // validation -- GUID 
            $user = User::where('id', $pledge->user_id)->first();
            if (!$user->guid ) {
                $this->LogMessage( "(SKIP) No GUID found in Transaction {$pledge->id} - " . json_encode( $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at'])) );
                $this->skip += 1;
                continue;
            }

            // One-time
            if ($pledge->one_time_amount != 0) { 
                $one_time_data = [
                    "@odata.type" => "CDataAPI.[employee_info]",
                    "date_posted" =>  Carbon::now()->format('Y-m-d'), // Carbon::now()->format('c'), 

                    "Donation_Type" => 'S',   // Always 'S" for Speical Campaign
                    'yearcd' => $pledge->yearcd,
                    'transaction_id' => $pledge->id,
                    'frequency' => 'One-Time',

                    "GUID" => $pledge->user->guid,
                    "Employee_Name" => $pledge->user->name,    
                    "Amount" =>  $pledge->one_time_amount,

                    "Deduction_Code" => "PECSPL",   // always "PECSPL" for special campaign deduction
                  
                    'pledge_start_date' => $pledge->deduct_pay_from,
                    'pledge_end_date' => $pledge->deduct_pay_from,
                ];

                $result = $this->sendData($one_time_data);
                       
                if ($result) {

                    ExportAuditLog::create([
                        'schedule_job_name' => $this->task->job_name,
                        'schedule_job_id' => $this->task->id,
                        'to_application' => 'PSFT',
                        'table_name' => 'special_campaign_pledges',
                        'row_id' => $pledge->id,
                        'row_values' => json_encode($pledge),
                    ]);

                    // Update the complete flag in pledge table
                    $pledge->ods_export_status = 'C';
                    $pledge->ods_export_at = Carbon::now()->format('c');
                    $pledge->save();

                    // Log Message                 
                    $this->LogMessage( "    Transaction {$pledge->id} has been sent - " . json_encode( $pledge->only(['id', 'organization_id','user_id','pecsf_id', 'yearcd', 'seqno', 'deduct_pay_from', 'cancelled_at'])) );
                    
                }
            }
        }

        $this->LogMessage("Sent data was completed");
        $this->LogMessage("ODS Processed - " . $this->row_count);
        $this->LogMessage("ODS Skip      - " . $this->skip);
        $this->LogMessage("ODS Success   - " . $this->success);
        $this->LogMessage("ODS failure   - " . $this->failure);
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
            
            throw new Exception($ex);

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
