<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PledgeExport;
use Carbon\Carbon;

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
        $pledgeData = PledgeExport::limit(20)->get();
         // dd($pledgeData);
        foreach($pledgeData as $data) {
            $pushData = [
                "@odata.type" => "CDataAPI.[employee_info]",
                "date_posted" => Carbon::now()->format('c'), // "2021-11-16T08:22:12.858-08:00",
                "GUID" => (string) md5($data->GUID . 'pecsf'),
                "Amount" => (string) $data->DonationAmount,
                "CampaignYear" => $data->year,
                "CRA_Business_Number" => $data->CRABN,
                "Deduction_Code" => $data->DonationTypeCode,
                "Donation_Type" => $data->DonationType,
                "Employee_Name" => $data->EMPLOYEE_NAME,
                "ORG_CRA_NAME" => $data->charity_name
            ];
            $this->pushToODS($pushData);
            // dd($pushData);
        }
        /* $sampleData = [
            "@odata.type" => "CDataAPI.[employee_info]",
            "date_posted" => "2021-11-16T08:22:12.858-08:00",
            "GUID" => "GUID_1",
            "Amount" => "20.20"
        ];
      
        
        dd($response->json()); */
        $this->info("Sent data");
        return 0;
    }

    private function pushToODS($data) {
        $response = Http::withBasicAuth(
            config('services.ods.username'),
            config('services.ods.token')
        )->post('https://analytics-testapi.psa.gov.bc.ca/apiserver/api.rsc/Datamart_PECSF_dbo_employee_info/', $data);
        return $response;
    }
}
