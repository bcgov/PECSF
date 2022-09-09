<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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

        return 0;
    }


    protected function UpdatePayCalendar()
    {
        try {
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

        if (time() - $this->last_refresh_time > 5) {
            $this->task->message = $this->message;
            $this->task->save();
    
            $this->last_refresh_time = time();
        }

    }
}
