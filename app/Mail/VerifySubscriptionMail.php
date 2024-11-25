<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerifySubscriptionMail extends Mailable
{
    public $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->view('emails.verify-subscription')
            ->subject('Verify Your Subscription')
            ->with(['url' => $this->verificationUrl]);
    }
}
