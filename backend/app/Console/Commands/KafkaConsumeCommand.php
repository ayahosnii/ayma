<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KafkaConsumer;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume Kafka messages';

    protected $consumer;

    public function __construct(KafkaConsumer $consumer)
    {
        parent::__construct();
        $this->consumer = $consumer;
    }

    public function handle()
    {
        $this->consumer->consume(config('kafka.topics.example'));
    }
}
