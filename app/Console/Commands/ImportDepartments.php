<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use App\Models\Department;

use App\Models\BusinessUnit;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportDepartments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Departments from BI';

    /* attributes for share in the command */
    protected $task;
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

        try {

            $this->task = ScheduleJobAudit::Create([
                    'job_name' => $this->signature,
                    'start_time' => Carbon::now(),
                    'status' => 'Processing',
            ]);

            $this->LogMessage( now() );
            $this->LogMessage("Task-- Update/Create - Department");
            $this->UpdateDepartment();

            $this->LogMessage( now() );

            // Update the Task Audit log
            $this->task->end_time = Carbon::now();
            $this->task->status = $this->status;
            $this->task->message = $this->message;
            $this->task->save();

        
        } catch (\Exception $ex) {

            // log message in system
            if ($this->task) {
                $this->task->status = 'Error';
                $this->task->end_time = Carbon::now();
                $this->task->message .= $ex->getMessage() . PHP_EOL;
                $this->task->save();
            }

            // send out email notification
            $notify = new \App\SharedLibraries\SendEmailNotification();
            $notify->job_id =  $this->task ? $this->task->id : null;
            $notify->job_name =  $this->signature;
            $notify->error_message = $ex->getMessage();
            $notify->send(); 

            // write message to the log  
            throw new Exception($ex);

        }

        return 0;
    }

    protected function UpdateDepartment()
    {

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_REPORT_DEPARTMENTS_BI_ENDPOINT'));

        if ($response->successful()) {
            $size = 1000;
            $data = json_decode($response->body())->value;
            $batches = array_chunk($data, $size);

            foreach ($batches as $key => $batch) {
                $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );
                foreach ($batch as $row) {

                    $business_unit = BusinessUnit::where('code', $row->business_unit_code)->first();

                    $rec = Department::updateOrCreate([
                        'bi_department_id' => $row->department_id,
                    ], [
                        'department_name' => $row->department_name,
                        'group' => $row->group,
                        'yearcd' => $row->yearcd,
                        'business_unit_code'=> $row->business_unit_code,
                        'business_unit_name' => $row->business_unit_name,
                        'business_unit_id' => $business_unit ? $business_unit->id : null,
                    ]);

                    $total_count += 1;

                    if ($rec->wasRecentlyCreated) {
                        $created_count += 1;
                    } elseif ($rec->wasChanged() ) {
                        $updated_count += 1;
                    } else {
                        // No Action
                    }

                }
            }

            $this->LogMessage('    Total Row count     : ' . $total_count  );
            $this->LogMessage('    Total Created count : ' . $created_count  );
            $this->LogMessage('    Total Updated count : ' . $updated_count  );

        } else {
            $this->status = 'Error';
            $this->LogMessage( $response->status() . ' - ' . $response->body() );
            
            throw new Exception( $response->status() . ' - ' . $response->body()   );
        }

    }

    protected function LogMessage($text)
    {

        $this->info( $text );

        // write to log message
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        // $this->task->save();

    }

}