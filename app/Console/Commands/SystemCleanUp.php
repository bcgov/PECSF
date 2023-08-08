<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SystemCleanUp extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SystemCleanUp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /* Variable for logging */
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
            $this->LogMessage("Task -- Clean Up External Files");
            $this->CleanUpExternalFiles();

            // Update the Task Audit log
            $this->task->end_time = Carbon::now();
            $this->task->status = $this->status;
            $this->task->message = $this->message;
            $this->task->save();

        
        } catch (Exception $ex) {

            // log message in system
            if ($this->task) {
                $this->task->status = 'Error';
                $this->task->end_time = Carbon::now();
                $this->task->message .= $ex->getMessage() . PHP_EOL;
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

        return Command::SUCCESS;

    }

    protected function CleanUpExternalFiles() {
    
        $this->LogMessage( "Step 1: Uploaded Import file (Charities) -- (location: /var/www/html/storage/app/charities/uploads/import) ");
        $this->LogMessage('');
        $path = storage_path('app');
        $files = File::files($path . '/charities/uploads/import');

        foreach($files as $file) {

            if ( date('Y-m-d H:i:s', $file->getMTime()) <= now()->subDays( env('REPORT_RETENTION_DAYS') ) ) {
                // print  $file->getFilename() . ' - ' . date('Y-m-d H:i:s', $file->getMTime()) . PHP_EOL;

                if (File::exists( $file->getPathname()  )) {
                    File::delete( $file->getPathname() );
                    $this->LogMessage( "  File - [" . $file->getPathname() . "] was deleted");                
                }
            }
        }

        $this->LogMessage('');
        $this->LogMessage( "Step 2: Charities Import Log file -- (location: /var/www/html/storage/logs) ");
        $this->LogMessage('');
        $path = storage_path('logs');
        $files = File::files($path);

        foreach($files as $file) {
            if ((Str::startsWith($file->getFilename() ,"charities_import_")) && (Str::endsWith($file->getFilename(), ".log")) && 
                (date('Y-m-d H:i:s', $file->getMTime()) <= now()->subDays( env('LOG_RETENTION_DAYS'))) ) {
                
                if (File::exists( $file->getPathname()  )) {
                    File::delete( $file->getPathname() );
                    $this->LogMessage( "  File - [" . $file->getPathname() . "] was deleted");                
                }
                
            }
        }

        $this->LogMessage('');
        $this->LogMessage( "Step 3: Laravel Log file  (location: /var/www/html/storage/logs) ");
        $this->LogMessage('');

        $path = storage_path('logs');
        $files = File::files($path);
        foreach($files as $file) {
            if ((Str::startsWith($file->getFilename() ,"laravel-")) && (Str::endsWith($file->getFilename(), ".log")) && 
                (date('Y-m-d H:i:s', $file->getMTime()) <= now()->subDays( env('LOG_RETENTION_DAYS'))) ) {
                
                if (File::exists( $file->getPathname()  )) {
                    File::delete( $file->getPathname() );
                    $this->LogMessage( "  File - [" . $file->getPathname() . "] was deleted");                
                }
                
            }
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
