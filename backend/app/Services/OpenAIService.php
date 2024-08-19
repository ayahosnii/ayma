<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('HUGGINGFACE_API_KEY');
    }

    public function generateResponse($prompt)
    {
        $response = $this->client->post('https://api-inference.huggingface.co/models/gpt2', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'inputs' => $prompt,
                'parameters' => [
                    'max_length' => 150,
                ],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
