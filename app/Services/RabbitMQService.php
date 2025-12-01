<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    public function publish($routingKey, $data)
    {
        $host = env('RABBITMQ_HOST', 'localhost');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $pass = env('RABBITMQ_PASSWORD', 'guest');
        $vhost = env('RABBITMQ_VHOST', '/');
        $exchange = env('RABBITMQ_EXCHANGE', 'agency_events');

        try {
            $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
            $channel = $connection->channel();

            // Declare exchange to ensure it exists
            $channel->exchange_declare($exchange, 'topic', false, true, false);

            $msg = new AMQPMessage(
                json_encode($data),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );

            $channel->basic_publish($msg, $exchange, $routingKey);

            $channel->close();
            $connection->close();
            
            Log::info("RabbitMQ: Published message to {$exchange} -> {$routingKey}");
            return true;
        } catch (\Exception $e) {
            Log::error('RabbitMQ Publish Error: ' . $e->getMessage());
            return false;
        }
    }
}
