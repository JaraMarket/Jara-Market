<?php

namespace App\Services\Kafka;

use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class KafkaProducerService
{
    public static function publish(string $topic, array $payload): void
    {
        $message = new Message(
            headers: [],
            body: $payload,
            key: $payload['key'] ?? null
        );

        Kafka::publishOn($topic)
            ->withMessage($message)
            ->send();
    }
}
