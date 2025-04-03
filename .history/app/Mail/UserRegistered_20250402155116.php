<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $customerData;

    public function __construct($customerData)
    {
        $this->customerData = $customerData;
    }

    public function build()
    {
        return $this->view('emails.customer_registered');
    }
}
