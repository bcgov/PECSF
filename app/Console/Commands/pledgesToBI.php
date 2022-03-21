<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Pledge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class pledgesToBI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pledgesToBI';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending pledge transactions to Datawarehouse vis ODS';

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
        $this->sendDonationTransactionsToDataWarehouse();

        return 0;
    }

    /**
     * Main Function for sending pledges transactions to Datawarehouse.
     *
     * @return int
     */
    private function sendDonationTransactionsToDataWarehouse() {
        $this->info("Sending data to BI (Datawarehouse)");

        $success = 0;
        $failure = 0;
        $pledgeData = Pledge::whereNull('bi_export_status')->orderBy('updated_at')->get();


        foreach($pledgeData as $pledge) {            

            foreach($pledge->charities as $pledgeCharity) {

                switch (strtolower($pledge->frequency))
                {
                    case 'one time':
                    case 'onetime':
                        $Donation_Type = "O";
                        $Deduction_Code = "PECSF1";
                        $campaign_year = $pledge->created_at->year;
                        $start_date = $pledge->updated_at->format('Y-m-d');
                        $end_date = $pledge->updated_at->format('Y-m-d');
                        $amount = $pledgeCharity->amount;
                        $goal_amount = $pledgeCharity->goal_amount;
                        break;
                    case 'bi-weekly':
                    case 'biweekly':
                        $Donation_Type = "B";
                        $Deduction_Code = "PECSF";
                        $campaign_year = $pledge->created_at->year;
                        $start_date = $pledge->updated_at->format('Y-m-d');
                        $end_date = $pledge->updated_at->format('Y-12-31');
                        $amount = $pledgeCharity->amount;
                        $goal_amount = $pledgeCharity->goal_amount;

                        break;
                    default:
                        break;                    
                }

                $pushData = [
                    //"@odata.type" => "CDataAPI.[employee_info]",
                    "campaign_year" => $campaign_year,
                    "cra_charity_business_reg_num" => $pledgeCharity->charity->registration_number,
                    "donation_type" => $Donation_Type,
                    "user_guid_id" => $pledge->user->guid,

                    "amount" =>  $amount,
                    "date_updated" => Carbon::now()->format('Y-m-d'), 
                    "date_deleted" => null,
                    "deduction_code" => $Deduction_Code,
                    "goal_amount" =>  $goal_amount,
                    "pledge_start_date" => $start_date,
                    "pledge_end_date" => $end_date,
                    "additional_info" => $pledgeCharity->additional,
                ];
                
               // Exception if guid is blank 
                if (!($pledge->user->guid)) { 
                    $this->info("Exception found: Pledge " . $pledge->id . ' Pledge Charity ' . $pledgeCharity->id . ' User ' . $pledge->user->email .
                        " without GUID." );
                }

                $response = Http::withBasicAuth(
                    env('ODS_USERNAME'),
                    env('ODS_TOKEN')
                )->post( env('ODS_OUTBOUND_PLEDGE_BI_ENDPOINT'), $pushData);

                if ($response->successful()) {
                    $pledge->bi_export_status = 'C';
                    $pledge->bi_export_at = Carbon::now()->format('c');
                    $pledge->save();
                    $success += 1;
                } else {
                    $failure += 1;
                }
            }

        }

        $this->info("Sent data complete");
        $this->info("Success - " . $success);
        $this->info("failure - " . $failure);
        return 0;

    }
}
