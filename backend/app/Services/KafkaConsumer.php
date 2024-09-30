<?php

namespace App\Services;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\ConsumerConfig;

class KafkaConsumer
{
    protected $consumer;

    public function __construct()
    {
        // Initialize Kafka consumer
        $conf = new \RdKafka\Conf();
        $conf->set('group.id', 'my-consumer-group');
        $conf->set('metadata.broker.list', 'localhost:9092'); // Set Kafka broker list

        // Initialize the consumer with the configuration
        $this->consumer = new Consumer($conf);
    }

    public function consume($topicName)
    {
        // Ensure the consumer is properly initialized
        if (!$this->consumer) {
            throw new \Exception("Kafka consumer is not initialized.");
        }

        // Create a new topic instance
        $topic = $this->consumer->newTopic($topicName);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_END);

        while (true) {
            $message = $topic->consume(0, 1000);

            if ($message === null) {
                continue; // Skip processing if message is null
            }

            if ($message->err) {
                echo "Error: " . $message->errstr() . PHP_EOL;
                continue; // Skip processing if there's an error
            }

            // Process the message
            echo "Received message: " . $message->payload . PHP_EOL;
        }
    }
}
