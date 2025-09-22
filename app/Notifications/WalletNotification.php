<?php

namespace App\Notifications;

use App\Enums\WalletTransactionTypeEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WalletNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type, // 'credit' or 'debit'
        public float $amount,
        public float $balance,
        public ?string $reference = null,
        public ?string $remarks = null
    ){}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->type === WalletTransactionTypeEnum::CREDIT()
            ? "Wallet Credited: ₦" . number_format($this->amount, 2)
            : "Wallet Debited: ₦" . number_format($this->amount, 2);

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.wallet', [
                'user'      => $notifiable,
                'type'      => $this->type,
                'amount'    => $this->amount,
                'balance'   => $this->balance,
                'reference' => $this->reference,
                'remarks'   => $this->remarks,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->formatPayload($notifiable);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->formatPayload($notifiable));
    }

    public function toArray($notifiable): array
    {
        return $this->formatPayload($notifiable);
    }

    protected function formatPayload($notifiable): array
    {
        $message = $this->type === 'credit'
            ? "₦" . number_format($this->amount, 2) . " was credited to your wallet."
            : "₦" . number_format($this->amount, 2) . " was debited from your wallet.";

        return [
            'message'   => $message,
            'type'      => $this->type,
            'amount'    => $this->amount,
            'balance'   => $this->balance,
            'reference' => $this->reference,
            'remarks'   => $this->remarks,
            'user_id'   => $notifiable->id,
        ];
    }
}
