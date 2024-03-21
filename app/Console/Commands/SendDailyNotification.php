<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily email notification';
    
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

        // June 24, replaceto send 
        $switch = env('EMAIL_DAILY_NOTIFICATION_ENABLED');
        if (!($switch)) {
            return true;
        }

        // $toAddresses = ['james.poon@gov.bc.ca', 'james.poon@telus.com',
        //                 'Kunal.Kapoor1@ca.ey.com',
        //                 'jpoon88@gmail.com', 'employee11@extest.gov.bc.ca',
        //                 'employee12@extest.gov.bc.ca'];
        $toAddresses = explode( ',', env('EMAIL_DAILY_NOTIFICATION_ADDRESSES') );   

        $subject = '(from region: '. env('APP_ENV') .') ' . env('APP_NAME') . ' - schedule daily notification testing (Ver 2.0)';
        $body = "Test message -- daily notification send out from server for testing purpose, please ignore. (from region: " . env('APP_ENV') .')';

        try {
            Mail::raw( $body , function($message) use($subject, $toAddresses) {
                $message->to( $toAddresses );
                $message->subject(  $subject );
            });  

            $this->info('Successfully sent daily test notification  to eligible people.');

        } catch (Exception $ex) {

            $this->info('Error. Failed to sent daily test notification to eligible people.');

        }            

        // check for failures
        // if (Mail::failures()) {
        //     // return response showing failed emails
        //     $this->info('Error. Failed to sent daily test notification to eligible people.');
        // } else {
        //     $this->info('Successfully sent daily test notification  to eligible people.');
        // }

        return 0;

    }

}
