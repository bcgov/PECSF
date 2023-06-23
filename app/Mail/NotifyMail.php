<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $body, $from_email;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($from_email,  $subject, $body)
    {
        //
        // $this->from = $from;
        $this->subject = $subject;
        $this->body = $body;
        $this->from_email = $from_email;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( $this->from_email  )
                    ->subject( $this->subject )
                    ->view('emails.notification-template');
    }
}
