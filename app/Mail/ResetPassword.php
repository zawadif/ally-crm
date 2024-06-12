<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $otp;
    public $user;
    public $logo;
    public function __construct($otp,$user)
    {
        $this->otp=$otp;
        $this->user=$user;
        $this->logo = URL::asset('img/logo/logo.png');

    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tennis Fights Password Reset')
        ->view('emails.resetPassword');
    }
}
