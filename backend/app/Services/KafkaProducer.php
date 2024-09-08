<?php

namespace App\Services;

use RdKafka\Producer;
use RdKafka\ProducerTopic;

class KafkaProducer
{
    protected $producer;

    public function __construct()
    {
        $this->producer = new Producer();
        $this->producer->addBrokers(config('kafka.brokers'));
    }

    public function send($topicName, $message)
    {
        $topic = $this->producer->newTopic($topicName);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->flush(10000);
    }
}
