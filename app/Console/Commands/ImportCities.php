<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use App\Models\EmployeeJob;
use App\Models\PledgeHistory;
use App\Models\City;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportCities extends Command
{

    protected $task;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportCities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the City Information from BI';

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
        ini_set('memory_limit', '4096M');

        try {

            $this->task = ScheduleJobAudit::Create([
                'job_name' => $this->signature,
                'start_time' => Carbon::now(),
                'status','Initiated'
            ]);

            $this->info( now() );
            $this->info("Update/Create - City Information");
            $this->UpdateCities();
            $this->info( now() );

            // Update the Task Audit log
            $this->task->end_time = Carbon::now();
            $this->task->status = 'Completed';
            $this->task->save();
        
        } catch (\Exception $ex) {

            // log message in system
            if ($this->task) {
                $this->task->status = 'Error';
                $this->task->end_time = Carbon::now();
                $this->task->message = $ex->getMessage() . PHP_EOL;
                $this->task->save();
            }

            // send out email notification
            $notify = new \App\MicrosoftGraph\SendEmailNotification();
            $notify->job_id =  $this->task ? $this->task->id : null;
            $notify->job_name =  $this->signature;
            $notify->error_message = $ex->getMessage();
            $notify->send(); 

            // write message to the log  
            throw new Exception($ex);

        }

        return 0;

    }

    protected function UpdateCities()
    {

        // Get the latest success job's start time
        $last_job = ScheduleJobAudit::where('job_name', $this->signature)
            ->where('status','Completed')
            ->orderBy('end_time', 'desc')->first();
        $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ;

        //$filter = 'date_updated gt \''.$last_start_time.'\' or date_deleted gt \''.$last_start_time.'\'';
        $filter = '';  // Disbaled the filter due to process timimg issue

        // try {

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_PS_TGB_CITY_TBL_ENDPOINT').'?$count=true&$top=1000'.'&$filter='.$filter);

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $key => $batch) {
                    $this->info( '    -- each batch (1000) $key - '. $key );
                    $this->info( '    -- count batch (1000) $key - '. count($batch));

                    foreach ($batch as $row) {
                        City::updateOrCreate([
                            'city' => $row->City,
                            'country' => $row->Country,
                            'province' => $row->Province,
                            'TGB_REG_DISTRICT' => $row->TGB_REG_DISTRICT,
                            'DescrShort' => $row->DescrShort
                        ],[
                            'city' => $row->City,
                            'country' => $row->Country,
                            'province' => $row->Province,
                            'TGB_REG_DISTRICT' => $row->TGB_REG_DISTRICT,
                            'DescrShort' => $row->DescrShort
                        ]);
                    }
                }
            } else {
                $this->info( $response->status() );
                $this->info( $response->body() );
            }

        // } catch (\Exception $ex) {

        //     // log message in system
        //     $this->task->status = 'Error';
        //     $this->task->end_time = Carbon::now();
        //     $this->task->message = $ex->getMessage() . PHP_EOL;
        //     $this->task->save();

        //     throw new Exception($ex);

        // }


    }



}
