<?php

namespace App\Notifications;


use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FailedPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $email, public array $data)
    { }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = "Unknown Payment Detected"; 
        
        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.failed_payment', [
                'user'  => $notifiable,
                'email' => $this->email,
                'data'  => $this->data,
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "A payment attempt from {$this->email} could not be verified.",
            'email'   => $this->email,
            'data'    => $this->data,
            'user_id' => $notifiable->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "A payment attempt from {$this->email} could not be verified.",
            'email'   => $this->email,
            'data'    => $this->data,
            'user_id' => $notifiable->id,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "A payment attempt from {$this->email} could not be verified.",
            'email'   => $this->email,
            'data'    => $this->data,
            'user_id' => $notifiable->id,
        ];
    }
}