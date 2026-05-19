<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->baseUrl = 'https://api.groq.com/openai/v1';
    }

    /**
     * Send a message to Groq with system context about the POS data.
     */
    public function chat(string $userMessage, string $systemContext): ?string
    {
        if (empty($this->apiKey)) {
            return 'AI service is not configured. Please add GROQ_API_KEY to your .env file.';
        }

        $url = "{$this->baseUrl}/chat/completions";

        $payload = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemContext,
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage,
                ],
            ],
            'model' => $this->model,
            'temperature' => 0.7,
            'max_completion_tokens' => 500,
            'top_p' => 1,
            'stream' => false,
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No response from AI.';
            }

            // Handle rate limiting
            if ($response->status() === 429) {
                $retryAfter = $response->header('retry-after');
                return "AI service rate limit exceeded. Please try again in {$retryAfter} seconds.";
            }

            return 'AI service error: ' . ($response->json('error.message') ?? 'Unknown error');
        } catch (\Exception $e) {
            return 'Could not reach AI service. Please try again later.';
        }
    }

    /**
     * Send a streaming chat message to Groq (for better user experience).
     * Note: This returns a stream that needs to be handled by the caller.
     */
    public function chatStream(string $userMessage, string $systemContext)
    {
        if (empty($this->apiKey)) {
            return 'AI service is not configured. Please add GROQ_API_KEY to your .env file.';
        }

        $url = "{$this->baseUrl}/chat/completions";

        $payload = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemContext,
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage,
                ],
            ],
            'model' => $this->model,
            'temperature' => 0.7,
            'max_completion_tokens' => 500,
            'top_p' => 1,
            'stream' => true,
        ];

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->withOptions([
                    'stream' => true,
                ])
                ->post($url, $payload);

            return $response;
        } catch (\Exception $e) {
            return 'Could not reach AI service. Please try again later.';
        }
    }

    /**
     * Get available models from Groq API.
     */
    public function getAvailableModels(): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                ])
                ->get("{$this->baseUrl}/models");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set a different model for the service.
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get current model being used.
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
