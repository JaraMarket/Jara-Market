<?php

namespace App\Notifications;

use App\Enums\UserPermissionsEnum;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCancelledNotification extends Notification implements ShouldQueue
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
        $subject = "Order Cancelled"; 
        
        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.order_cancelled', [
                'user'  => $notifiable,
                'order' => $this->order,
                'message' => $this->getMessage($notifiable)
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->getMessage($notifiable),
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message'   => $this->getMessage($notifiable),
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
        ]);
    }

    protected function getMessage($notifiable): string
    {
        if ($notifiable->role === UserPermissionsEnum::VENDOR()) {
            return "Order #{$this->order->reference} from your store has been cancelled.";
        }

        if ($notifiable->role === UserPermissionsEnum::ADMIN()) {
            return "Order #{$this->order->reference} has been cancelled.";
        }

        // Default: customer
        return "Your order #{$this->order->reference} has been cancelled.";
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
            'message'   => $this->getMessage($notifiable),
            'order_id'  => $this->order->id,
            'status'    => $this->order->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
        ];
    }
}