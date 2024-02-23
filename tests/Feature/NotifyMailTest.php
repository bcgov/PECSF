<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\NotifyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotifyMailTest extends TestCase
{

    public function test_request_sends_notify_mail_on_success() {

      Mail::fake();

      // Assert that no mailables were sent...
      Mail::assertNothingSent();
       
      // $response = $this->call('POST', 'some/password/reset/path',[
      // 	'email' => $user->email
      // ]);
      $to = 'dummy@example.com';

      Mail::to( $to )->send(new NotifyMail( 'no-reply@example.com', 'Testing', 'This is a body' ));

      //Check that mail was sent to test address
      Mail::assertSent( NotifyMail::class , function ($mail) use($to) {
          return $mail->hasTo( $to ) &&
                 $mail->hasSubject('Testing');
      });

    }

}
