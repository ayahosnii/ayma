<?php

namespace App\Console\Commands;

use App\Events\OrderDataRecived;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class ProcessKafkaMessages extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume Kafka messages for inventory updates';

    public function handle()
    {
        $consumer = Kafka::createConsumer((array)'orders-topic')->subscribe('orders-topic');

        $consumer->withHandler(function(KafkaConsumerMessage $message) {
            try {
                event(new OrderDataRecived(json_encode($message->getBody())));
            } catch (\Exception $e) {
                Log::error('Failed to process Kafka message: ' . $e->getMessage());
            }
        });

    }

}
