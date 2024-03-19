<?php

namespace App\SharedLibraries;

use DateTime;
use DateInterval;
use DateTimeZone;
use App\Models\User;

use App\Mail\NotifyMail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class SendEmailNotification
{

    //public $toAddresses;
    public $toRecipients;       /* array -- email addresses */
    public $ccRecipients;       /* array -- email addresses */
    public $bccRecipients;      /* array -- email addresses */
    
    public $subject;            /* String */
    public $body;               /* String */

    public $job_id;
    public $job_name;
    public $error_message;

    public $bodyContentType;    /* text or html, default is 'html' */

    // Audit Log related
    public $saveToLog;          /* Boolean -- true or false */
    
  
    public function __construct() 
    {

        $this->toRecipients = [];
        $this->ccRecipients = [];
        $this->bccRecipients = [];

        $this->subject = '';   
        $this->body = '';   

        $this->job_id = '';
        $this->job_name = '';
        $this->error_message = '';

        $this->bodyContentType = 'html';
        $this->saveToLog = true;

    }

    public function send() 
    {
        
        $switch = env('EMAIL_NOTIFICATION_ENABLED');
        if (!($switch)) {
            return true;
        }

        if (!($this->subject)) {
            $this->subject = "The Process (";
            $this->subject .=  $this->job_id ? $this->job_id  . " - " : ''; 
            $this->subject .=  $this->job_name; 
            $this->subject .=  ") was failed to complete.";
        }

        $this->toRecipients = env('EMAIL_NOTIFICATION_EMAIL_ADDRESSES');   

        return $this->sendMailUsingSMPTServer();

    }

    protected function sendMailUsingSMPTServer() 
    {

        $this->subject = "PECSF [". App::environment() . "] -- " . $this->subject;

        $this->body = "<p>";
        if ($this->job_id) {
            $this->body .= "Process ID   : " . $this->job_id . "</br>";
        }
        if (!str_contains($this->subject, "ALERT")) {
            $this->body .= "Process Name : " . $this->job_name  . "</br>";
            $this->body .= "Failed at    : " . now()  . "</br>";
            $this->body .= "</p>";
            $this->body .= "<p>The process was failed with the following error message: </p>";
        }
        $this->body .= "<p>" . $this->error_message . "</p>";
 
        // Send immediately
        $from =  env('MAIL_FROM_ADDRESS');
        $toAddresses = explode(",", $this->toRecipients);
        $subject = $this->subject;
        $body = $this->body;

        Mail::to( $toAddresses )->send(new NotifyMail( $from, $subject, $body ));

        if ($this->saveToLog) {
            // append onto Notification log file
            Log::channel('smtp')->info("Process (" . $this->job_id  . " - ". $this->job_name . ") failure notification message sent out to " . $this->toRecipients );
         
        }   

        return true;

    }

}
