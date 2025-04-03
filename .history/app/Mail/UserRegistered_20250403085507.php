<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $UserData;

    public function __construct($UserData)
    {
        $this->customerData = $customerData;
    }

    public function build()
    {
        return $this->view('emails.customer_registered');
    }
}
