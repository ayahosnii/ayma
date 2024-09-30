<?php

namespace App\Services;

use Junges\Kafka\Config\Config;
use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Message;

class KafkaConsumerService
{
    protected $consumer;
    protected $topicName;

    public function __construct($topicName)
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', config('kafka.brokers'));
        $conf->set('group.id', config('kafka.group_id'));
        $this->consumer = new Consumer($conf);
        $this->topicName = $topicName;
    }

    public function consume()
    {
        $topic = $this->consumer->newTopic($this->topicName);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_END);

        while (true) {
            $message = $topic->consume(0, 1000); // 1000 ms timeout
            if ($message->err) {
                if ($message->err == RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                    continue;
                }
                throw new \Exception($message->errstr());
            }

            $this->processMessage($message);
        }
    }

    protected function processMessage(Message $message)
    {
        // Process the message
        // Example: Handle the order creation
        $orderData = json_decode($message->payload, true);
        if ($orderData) {
            // You can use a service or directly handle order creation
            // Example: Order::create($orderData);
            \Log::info('Order message received: ', $orderData);
        }
    }
}
