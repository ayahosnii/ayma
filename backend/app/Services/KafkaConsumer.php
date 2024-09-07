<?php

namespace App\Services;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Message;

class KafkaConsumer
{
    protected $consumer;

//    public function __construct()
//    {
//        $this->consumer = new \RdKafka\Consumer();
//        $this->consumer->addBrokers(config('kafka.brokers'));
//        $this->consumer->setGroupId(config('kafka.group_id'));
//    }

    public function consume($topicName)
    {
        $topic = $this->consumer->newTopic($topicName);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_END);

        while (true) {
            $message = $topic->consume(0, 1000);
            if ($message->err) {
                continue;
            }

            // Process the message
            echo $message->payload . "\n";
        }
    }
}
