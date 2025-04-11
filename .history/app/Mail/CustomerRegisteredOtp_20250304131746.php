<?php

// app/Mail/CustomerRegistered.php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegisteredOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $otp;

    public function __construct($customer,$otp)
    {
        $this->data = $data;
        $this->otp = $otp;

    }

    public function build()
    {
        return $this->view('emails.customer_registered_otp');
    }
}
