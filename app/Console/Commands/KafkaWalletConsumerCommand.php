<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use App\Notifications\WalletNotification;
use App\Models\User;

class KafkaWalletConsumerCommand extends Command
{
    protected $signature = 'kafka:consume-wallet';
    protected $description = 'Consume wallet transaction events from Kafka and notify users';

    public function handle(): void
    {
        $consumer = Kafka::consumer()
            ->subscribe('wallet-events')
            ->withHandler(function ($message) {
                $data = json_decode($message->getBody(), true);

                $user = User::find($data['user_id'] ?? null);

                if ($user) {
                    $user->notify(new WalletNotification(
                        type: $data['type'],
                        amount: $data['amount'],
                        balance: $data['balance'],
                        reference: $data['reference'] ?? null,
                        remarks: $data['remarks'] ?? null
                    ));
                }
            })
            ->build();

        $this->info("Listening to wallet-events...");
        $consumer->consume();
    }
}
