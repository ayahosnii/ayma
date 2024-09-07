<?php

return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
    'group_id' => env('KAFKA_GROUP_ID', 'my-group'),
    'topics' => [
        'example' => env('KAFKA_TOPIC_EXAMPLE', 'example_topic'),
    ],
];
