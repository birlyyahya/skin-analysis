<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AnthropicAIService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct($apiKey, $endpoint)
    {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    public function generateAnalysis($prompt)
    {
        // Log prompt details
        logger()->info('====== PROMPT DETAILS ======');
        logger()->info('Prompt content: ' . $prompt);
        logger()->info('Prompt length: ' . strlen($prompt));

        $payload = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        logger()->info('Formatted message: ', $payload);

        try {
            $response = Http::withHeaders([
                'anthropic-version' => '2023-06-01',
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, $payload);

            // Parse the API response
            $result = $response->json();

            // Handle API errors
            if ($response->failed() || isset($result['error'])) {
                $errorMessage = $result['error']['message'] ?? 'Unknown API error';
                throw new \Exception("Claude API Error: $errorMessage");
            }

            return $result['content'][0]['text'] ?? '';
        } catch (\Exception $e) {
            logger()->error('API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
