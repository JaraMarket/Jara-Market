<?php

namespace App\Notifications;


use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $order)
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
        $subject = "Order Confirmation"; 
        
        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.order_confirmation', [
                'user'  => $notifiable,
                'order' => $this->order
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message'   => "Your order #{$this->order->reference} has been placed successfully.",
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message'   => "Your order #{$this->order->reference} has been placed successfully.",
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
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
            'message'   => "Your order #{$this->order->reference} has been placed successfully.",
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
        ];
    }
}