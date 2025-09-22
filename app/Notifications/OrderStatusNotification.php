<?php

namespace App\Notifications;


use App\Enums\OrderNotificationTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public $order,
        public $recipientType, // 'customer', 'vendor', 'admin'
        public $status         // 'processing', 'completed', etc.
    ) {}

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
    public function toMail($notifiable): MailMessage
    {
        $subject = match ($this->status) {
            StatusEnum::PROCESSING() => "Order #{$this->order->reference} is Processing",
            StatusEnum::COMPLETED()  => "Order #{$this->order->reference} is Ready",
            default                  => "Order #{$this->order->reference} Update",
        };

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.order_status', [
                'user'          => $notifiable,
                'order'         => $this->order,
                'recipientType' => $this->recipientType,
                'status'        => $this->status,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->formatMessage($notifiable);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->formatMessage($notifiable));
    }

    public function toArray($notifiable): array
    {
        return $this->formatMessage($notifiable);
    }

    protected function formatMessage($notifiable): array
    {
        $message = match ($this->recipientType) {
            OrderNotificationTypeEnum::CUSTOMER() => match ($this->status) {
                StatusEnum::PROCESSING() => "Your order #{$this->order->reference} is now processing.",
                StatusEnum::COMPLETED()  => "Your order #{$this->order->reference} is ready for delivery.",
                default => "Update for your order #{$this->order->reference}.",
            },
            OrderNotificationTypeEnum::VENDOR() => "You have accepted items from order #{$this->order->reference}.",
            OrderNotificationTypeEnum::ADMIN()  => "Order #{$this->order->reference} has a status update: {$this->status}.",
            default => "Order #{$this->order->reference} update.",
        };

        return [
            'message'   => $message,
            'order_id'  => $this->order->id,
            'status'    => $this->status,
            'total'     => $this->order->total,
            'user_id'   => $notifiable->id,
            'recipient' => $this->recipientType,
        ];
    }
}