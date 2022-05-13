<?php

namespace App\Console\Commands;

use stdClass;
use Carbon\Carbon;
use App\Models\Pledge;
use App\Models\Charity;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class CharitiesToBI extends Command
{


    protected $success;
    protected $failure;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:charitiesToBI';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending charity information to Datawarehouse vis ODS';

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

        $this->sendCharitiesToDataWarehouse();

        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();


        return 0;
    }

    /**
     * Main Function for sending pledges transactions to Datawarehouse.
     *
     * @return int
     */
    private function sendCharitiesToDataWarehouse() {
        $this->info("Sending Charity Detail to BI (Datawarehouse)");

        $this->success = 0;
        $this->failure = 0;
        $n = 0;
        
        $job  = ScheduleJobAudit::where('job_name', $this->signature)
                    ->where('status','Completed')
                    ->orderBy('end_time', 'desc')->first();

        $last_start_time = $job ? $job->start_time : '2000-01-01' ; 

        if ($job) {
            // This is a delta program
            $data = Charity::where('updated_at', '>=', $last_start_time)->orderBy('id')
                ->chunk(5000, function($charities) use( &$n ) {
                    $this->info( "batch (5000) - " .$n++ );
                    $this->info( now() );

                    $data = $this->prepareData($charities);

                    $pushdata = new stdClass();
                    $pushdata->delta_ind = 1;
                    $pushdata->charity_info = json_encode($data);

                    $this->sendData( $pushdata );
            });
            
        } else {

            $data = Charity::orderBy('id')
            ->chunk(1000, function($charities) use( &$n ) {
                  $this->info( "batch (1000) - " .$n++ );
                  $this->info( now() );

                  //dd( json_encode($data) );
                  // foreach ($charities as $charity) {
                  //      // apply some action to the chunked results here
                  //     $this->sendData($charity);
                  // }
                  $data = $this->prepareData($charities);
                  $pushdata = new stdClass();
                  $pushdata->charity_info = json_encode($data);
                  
                  $this->sendData( $pushdata );
            });

        }
    
        $this->info( now() );
        $this->info("Sent data complete");
        $this->info("Success - " . $this->success);
        $this->info("failure - " . $this->failure);

        return 0;

    }

    protected function prepareData($charities) {

        $charites_array = [];
        foreach ($charities as $charity) {
            // apply some action to the chunked results here
            array_push($charites_array,
             [
                //"@odata.type" => "CDataAPI.[employee_info]",
                "charity_id" => $charity->id, 
                "bn_registration_number" => $charity->registration_number,
                "charity_name" => $charity->charity_name,
                "charity_status" => $charity->charity_status,
                "effective_date_of_status" => $charity->effective_date_of_status->format('Y-m-d'),
                "sanction" => $charity->sanction,
                "designation" => $charity->designation_code,
                "category" => $charity->category_code,
                "address" => $charity->address,
                "city" => $charity->city,
                "province_territory_outside_of_canada"  => $charity->province,
                "country" => $charity->country,
                "postal_code_zip_code" => $charity->postal_code,
                "date_updated" => $charity->updated_at,
             ]
            );
        }

        return $charites_array;

    }



    protected function sendData($pushdata) {

        // $pushData = [
        //     //"@odata.type" => "CDataAPI.[employee_info]",
        //     "charity_id" => $charity->id, 
        //     "bn_registration_number" => $charity->registration_number,
        //     "charity_name" => $charity->charity_name,
        //     "charity_status" => $charity->charity_status,
        //     "effective_date_of_status" => $charity->effective_date_of_status->format('Y-m-d'),
        //     "sanction" => $charity->sanction,
        //     "designation" => $charity->designation_code,
        //     "category" => $charity->category_code,
        //     "address" => $charity->address,
        //     "city" => $charity->city,
        //     "province_territory_outside_of_canada"  => $charity->province,
        //     "country" => $charity->country,
        //     "postal_code_zip_code" => $charity->postal_code,
        //     "date_updated" => $charity->updated_at,
        // ];
        
        // $pushdata = new stdClass();
        // $pushdata->charity_info = json_encode($data);

        $response = Http::withBasicAuth(
            env('ODS_USERNAME'),
            env('ODS_TOKEN')
        )->withBody( json_encode($pushdata), 'application/json')
        ->post( env('ODS_OUTBOUND_CHARITY_BI_ENDPOINT') );

        if ($response->successful()) {
            $this->success += 1;
        } else {
                                    
            $this->info( $response->status() );
            $this->info( $response->body() );
            dd( json_encode($data) );
            //$this->info( "Failed : " . print_r($response) );
            $this->failure += 1;
        }

    }

}
