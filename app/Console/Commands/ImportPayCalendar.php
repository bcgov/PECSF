<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use App\Models\PayCalendar;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportPayCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportPayCalendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Pay Calendar from PeopleSoft via ODS';

     /* attributes for share in the command */
     protected $task;
     protected $created_count;
     protected $updated_count;
     protected $message;
     protected $status;

     protected $last_refresh_time;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->created_count = 0;
        $this->updated_count = 0;
        $this->message = '';
        $this->status = 'Completed';

        $this->last_refresh_time = time();
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
            $this->LogMessage("Task -- Update/Create - Pay Calendar");
            $this->UpdatePayCalendar();
            $this->LogMessage( now() );   

            $this->LogMessage( '' );
            $this->LogMessage( 'Total new created row(s) : ' . $this->created_count );
            $this->LogMessage( 'Total Updated row(s) : ' . $this->updated_count );
            $this->LogMessage( '' );

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


    protected function UpdatePayCalendar()
    {

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_PAY_CALENDAR_BI_ENDPOINT'));

        if ($response->successful()) {
            $data = json_decode($response->body())->value;

            foreach ($data as $row) {

                    $pyc = PayCalendar::updateOrCreate([
                        'pay_end_dt' => $row->pay_end_dt,
                    ], [
                        'pay_begin_dt' => $row->pay_begin_dt,
                        'check_dt' => $row->check_dt,
                        'close_dt' => $row->close_dt,
                    ]);


                    if ($pyc->wasRecentlyCreated) {

                        $this->created_count += 1;
                        $this->LogMessage('(CREATED) => pay_end_dt  | ' . $pyc->pay_end_dt . ' | ' . $pyc->pay_begin_dt . ' | ' . $pyc->check_dt . ' | ' . $pyc->close_dt );


                    } elseif ($pyc->wasChanged() ) {

                        $this->updated_count += 1;

                        $this->LogMessage('(UPDATED) => pay_end_dt | ' . $pyc->pay_end_dt );
                        $changes = $pyc->getChanges();
                        unset($changes["updated_at"]);
                        $this->LogMessage('  summary => '. json_encode( $changes ) );

                    } else {
                        // No Action
                    }
                
            }


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

        if (time() - $this->last_refresh_time > 5) {
            $this->task->message = $this->message;
            // $this->task->save();
    
            $this->last_refresh_time = time();
        }

    }
}
