<?php

namespace App\Console\Commands;

use App\Enums\OrderNotificationTypeEnum;
use Illuminate\Console\Command;
use App\Notifications\OrderStatusNotification;
use Junges\Kafka\Facades\Kafka;
use App\Models\User;

class KafkaOrderConsumerCommand extends Command
{
    protected $signature = 'kafka:consume-orders';
    protected $description = 'Consume order-related events from Kafka and trigger notifications';

    public function handle()
    {
        $consumer = Kafka::consumer()
            ->subscribe('order-events')
            ->withHandler(function ($message) {
                $data = json_decode($message->getBody(), true);

                // Example: Notify user when order is placed/updated
                if (isset($data['user_id'])) {
                    $user = User::find($data['user_id']);

                    if ($user) {
                        $user->notify(new OrderStatusNotification(
                            order: $data['order'],
                            recipientType: $data['recipient_type'] ?? OrderNotificationTypeEnum::CUSTOMER(),
                            status: $data['status'],
                        ));
                    }
                }
            })
            ->build();

        $this->info("Listening to order-events...");
        $consumer->consume();
    }
}
