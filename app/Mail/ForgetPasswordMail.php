<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->data = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $data = $this->data;
        $default_email_source = "noreply@support.com";
        return $this->from(strval(env('MAIL_FROM_ADDRESS', $default_email_source)))
        ->view('mail.forget', compact('data'))->subject("Reset Password Link");
    }
}
