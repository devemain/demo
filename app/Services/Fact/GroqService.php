<?php
/**
 * 2025 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2025 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Services\Fact;

use App\Services\LoggerService;
use Illuminate\Support\Facades\Http;
use Throwable;

class GroqService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct(
        protected LoggerService $logger
    ) {
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model = config('services.groq.model');
    }

    public function callGroqApi(string $prompt, int $maxTokens = 100): ?string
    {
        $this->logger->setCaller(__METHOD__);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->timeout(30)
                ->post($this->apiUrl, [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'model' => $this->model,
                    'temperature' => 0.7,
                    'max_completion_tokens' => $maxTokens,
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;

                return $this->cleanResponse($content);
            }

            $this->logger->warning('Groq API attempt failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (Throwable $e) {
            $this->logger->error('Groq API error', [
                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    public function createPrompt(int $count = 10): string
    {
        return <<<PROMPT
            Generate $count different interesting facts about technology or internet.
            Each fact should be 1 sentence. Make them diverse and factual.
            Return as JSON array: ['fact1', 'fact2', ...]
            PROMPT;
    }

    public function generateFactsArray(int $count = 10): ?array
    {
        $this->logger->setCaller(__METHOD__);

        $response = $this->callGroqApi($this->createPrompt($count), 500);

        if (!$response) {
            return null;
        }

        try {
            $facts = json_decode($response, true);

            // If it's a string, we try to find the JSON inside
            if (is_string($facts) || !is_array($facts)) {
                preg_match('/\[.*\]/s', $response, $matches);
                if (isset($matches[0])) {
                    $facts = json_decode($matches[0], true);
                }
            }

            if (is_array($facts)) {
                return array_slice(array_map('trim', $facts), 0, $count);
            }

        } catch (Throwable $e) {
            $this->logger->error('Failed to parse facts JSON', [
                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    public function generateSingleFact(): ?string
    {
        $prompt = 'Generate one interesting fact about technology. One sentence only.';
        return $this->callGroqApi($prompt);
    }

    protected function cleanResponse(?string $response): ?string
    {
        if (!$response) {
            return null;
        }

        // Remove markdown
        $response = str_replace(['```json', '```', '**', '*', '`'], '', $response);

        // Normalize whitespace
        $response = preg_replace('/\s+/', ' ', trim($response));

        return trim($response);
    }

    public function testConnection(): bool
    {
        $response = $this->callGroqApi('Say "OK" if you are working.', 10);
        return !empty($response) && stripos($response, 'OK') !== false;
    }
}
