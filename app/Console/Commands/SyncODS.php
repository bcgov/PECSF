<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Pledge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncODS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info("Sending POST data");

        // Testing purpose 
        // $this->cleanUpODS();


        //$pledgeData = PledgeExport::limit(20)->get();
         // dd($pledgeData);

        $success = 0;
        $failure = 0;
        $pledgeData = Pledge::where('ods_export_status', null)->orderBy('updated_at')->get();
        foreach($pledgeData as $pledge) {

            switch ($pledge->frequency)
            {
                case 'one time':
                    $Donation_Type = "O";
                    $Deduction_Code = "PECSF1";
                    $start_date = $pledge->updated_at->format('Y-m-d');
                    $end_date = $pledge->updated_at->format('Y-m-d');
                    $amount = $pledge->amount;
                    break;
                case 'bi-weekly':
                    $Donation_Type = "B";
                    $Deduction_Code = "PECSF";
                    $start_date = $pledge->updated_at->format('Y-m-d');
                    $end_date = $pledge->updated_at->format('Y-12-31');
                    $amount = $pledge->amount;
                    break;
                default:
                    break;                    
            }

            $pushData = [
                "@odata.type" => "CDataAPI.[employee_info]",
                "date_posted" => Carbon::now()->format('c'), 
                "GUID" => $pledge->user->guid,
                "Employee_Name" => $pledge->user->name,    
                "Amount" =>  $amount,
                "Donation_Type" => $Donation_Type,
                "Deduction_Code" => $Deduction_Code,
                "pledge_start_date" => $start_date,
                "pledge_end_date" => $end_date,

            //     "@odata.type" => "CDataAPI.[employee_info]",
            //     "date_posted" => Carbon::now()->format('c'), // "2021-11-16T08:22:12.858-08:00",
            //     "GUID" => (string) md5($data->GUID . 'pecsf'),
            //     "Amount" => (string) $data->DonationAmount,
            //     "CampaignYear" => $data->year,
            //     "CRA_Business_Number" => $data->CRABN,
            //     "Deduction_Code" => $data->DonationTypeCode,
            //     "Donation_Type" => $data->DonationType,
            //     "Employee_Name" => $data->EMPLOYEE_NAME,
            //     "ORG_CRA_NAME" => $data->charity_name
            ];
            
            $response = $this->pushToODS($pushData);

            if ($response->successful()) {
                $pledge->ods_export_status = 'C';
                $pledge->ods_export_at = Carbon::now()->format('c');
                $pledge->save();
                $success += 1;
            } else {
                $failure += 1;
            }

        }
        /* $sampleData = [
            "@odata.type" => "CDataAPI.[employee_info]",
            "date_posted" => "2021-11-16T08:22:12.858-08:00",
            "GUID" => "GUID_1",
            "Amount" => "20.20"
        ];
        
        dd($response->json()); */
        $this->info("Sent data complete");
        $this->info("Success - " . $success);
        $this->info("failure - " . $failure);
        return 0;
    }

    private function pushToODS($data) {
        $response = Http::withBasicAuth(
            config('services.ods.username'),
            config('services.ods.token')
        )->post('https://analytics-testapi.psa.gov.bc.ca/apiserver/api.rsc/Datamart_PECSF_dbo_employee_info/', $data);
        return $response;
    }


    private function cleanUpODS() {

        Pledge::where('ods_export_status','C')->update(['ods_export_status' => '', 'ods_export_at' => null]);
        
        $response = Http::withBasicAuth(
          config('services.ods.username'),
          config('services.ods.token')
        )->get('https://analytics-testapi.psa.gov.bc.ca/apiserver/api.rsc/Datamart_PECSF_dbo_employee_info/');

        $responseBody = json_decode($response->getBody(), true);
        $pledgeData = $responseBody['value'];

        foreach($pledgeData as $item)
        {

            $guid = $item['GUID'];
            $date_posted = $item['date_posted'];
            $Donation_Type = $item['Donation_Type'];

            if ($response->successful() ) {

                if ($date_posted >= date('2022-02-15')) {
                    $parms = "(Donation_Type='$Donation_Type',GUID='$guid',date_posted='$date_posted')";
                    $response = Http::withBasicAuth(
                        config('services.ods.username'),
                        config('services.ods.token')
                    )->delete('https://analytics-testapi.psa.gov.bc.ca/apiserver/api.rsc/Datamart_PECSF_dbo_employee_info/'.$parms);

                    if ($response->failed() ) {
                        $this->info('failure');
                    }
                }
            } else {
                $this->info('failure');
            }

        }
        
    } 

}
