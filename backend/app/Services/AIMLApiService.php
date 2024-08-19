<?php

namespace App\Services;

use GuzzleHttp\Client;

class AIMLApiService
{
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiKey = env('AIML_API_KEY');
        $this->client = new Client([
            'base_uri' => 'https://api.aimlapi.com',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function generateText($prompt, $model, $messages = [])
    {
        // Ensure each message is an array with 'role' and 'content' properties
        $formattedMessages = array_map(function($message) {
            // If message is a string, assume it's user input and assign the 'user' role
            if (is_string($message)) {
                return [
                    'role' => 'system',
                    'content' => $message
                ];
            } elseif (is_array($message) && isset($message['role']) && isset($message['content'])) {
                // If message is already formatted correctly, leave it unchanged
                return $message;
            } else {
                // If message format is not recognized, assume it's user input and assign the 'user' role
                return [
                    'role' => 'system',
                    'content' => $message
                ];
            }
        }, $messages);

        $params = [
            'model' => $model,
            'messages' => $formattedMessages,
        ];

        $response = $this->client->post('/chat/completions', [
            'json' => $params,
        ]);

        return $response;
    }

}
