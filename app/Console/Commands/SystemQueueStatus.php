<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\DB;

class SystemQueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:queueStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitoring the status of job queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
         // Get the names of the configured queues

        try {

            $queues = DB::table(config('queue.connections.database.table'))->get();

            if($queues){

                foreach($queues as $queue) {

                    $t = Carbon::parse($queue->available_at);
                    $t->setTimezone('America/Vancouver');

                    // if more than 5 minutes
                    if (Carbon::now()->diffInSeconds($t) > 300) {
                        // Notify the administrator 
                        $payload = json_decode($queue->payload, true);

                        echo $payload['displayName'] .PHP_EOL;;
                        echo $payload['data']['command'] .PHP_EOL;
                                                                
                        // send out email notification
                        $notify = new \App\MicrosoftGraph\SendEmailNotification();
                        $notify->job_id =  null;
                        $notify->job_name = $this->signature;
                        $notify->subject = "**ALERT** The background job was queued up over 5 mins"; 
                        $notify->error_message = "The background queue process ". $payload['displayName'] . " has been in the queue for more than 5 minutes." . "</br>";
                        $notify->error_message .= "Please kindly check the process queue by using <strong>ps -eF </strong>command.". "</br>";
                        $notify->error_message .= "</br>";
                        $notify->error_message .= "The job detail as below : " .  "</br>";
                        $notify->error_message .= "</br>";
                        $notify->error_message .= $payload['data']['command'];
                        $notify->send(); 
                    }
                }

            }

        } catch (\Exception $ex) {

            // send out email notification
            $notify = new \App\MicrosoftGraph\SendEmailNotification();
            $notify->job_id =  null;
            $notify->job_name = $this->signature;
            $notify->error_message = $ex->getMessage();
            $notify->send(); 

            // write message to the log  
            throw new Exception($ex);
        }        

    }

}

