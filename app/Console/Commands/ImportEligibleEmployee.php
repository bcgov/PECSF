<?php

namespace App\Console\Commands;

use App\Models\DonorByDepartment;
use App\Models\ElligibleEmployee;
use Carbon\Carbon;
use App\Models\EmployeeJob;
use App\Models\PledgeHistory;
use App\Models\City;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportEligibleEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportEligibleEmployees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the Eligible Employee Information from BI';

    // Shared attribute for logging message
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
            'status' => 'Processing'
        ]);

        $this->LogMessage( now() );
        $this->LogMessage("Update/Create - EE Information");
        $this->UpdateEE();
        $this->LogMessage( now() );

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;

    }

    protected function UpdateEE()
    {

        // Get the latest success job's start time
        $last_job = ScheduleJobAudit::where('job_name', $this->signature)
            ->where('status','Completed')
            ->orderBy('end_time', 'desc')->first();
        $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ;

        //$filter = 'date_updated gt \''.$last_start_time.'\' or date_deleted gt \''.$last_start_time.'\'';
        $filter = '';  // Disbaled the filter due to process timimg issue
        ElligibleEmployee::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_PECSF_ELIGIBLE_EMPLOYEE_ENDPOINT').'?$count=true&$top=1000'.'&$filter='.$filter);

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( '    -- count batch (1000) - '. count($batch));

                    foreach ($batch as $row) {
                        ElligibleEmployee::updateOrCreate([
                            'as_of_date' => $row->as_of_date,
                            'ee_count' => $row->ee_cnt,
                            'business_unit' => $row->business_unit,
                            'business_unit_name' => $row->business_unit_name,
                            'cde' => $row->cde,
                            'year' => $row->year
                        ],[
                            'as_of_date' => $row->as_of_date,
                            'ee_count' => $row->ee_cnt,
                            'business_unit' => $row->business_unit,
                            'business_unit_name' => $row->business_unit_name,
                            'cde' => $row->cde,
                            'year' => $row->year
                        ]);
                    }
                }
            } else {
                $this->LogMessage( 'Status : ' . $response->status() . ' - ' . $response->body() );
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
