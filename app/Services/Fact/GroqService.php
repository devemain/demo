<?php

declare(strict_types=1);

/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 *
 * @link      https://github.com/DeveMain
 */

namespace App\Services\Fact;

use App\Services\Fact\Contracts\AIServiceInterface;
use App\Services\Logging\Contracts\LoggerInterface;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Handles API calls, prompt creation, and fact generation.
 */
class GroqService implements AIServiceInterface
{
    /**
     * API key for authentication with the Groq API.
     */
    protected string $apiKey;

    /**
     * Base URL for the Groq API endpoints.
     */
    protected string $apiUrl;

    /**
     * Model name to use for the Groq API requests.
     */
    protected string $model;

    /**
     * Creates a new instance.
     *
     * @param  LoggerInterface  $logger  Logger service for logging API calls and errors
     */
    public function __construct(
        protected readonly LoggerInterface $logger
    ) {
        // Load configuration values
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model = config('services.groq.model');
    }

    /**
     * Call the Groq API with a given prompt.
     *
     * @param  string  $prompt  The prompt to send to the API
     * @param  int  $maxTokens  Maximum number of tokens in the response
     * @return ?string The API response or null if failed
     */
    public function callGroqApi(string $prompt, int $maxTokens = 100): ?string
    {
        // Set the current method as the caller for logging purposes
        $this->logger->setCaller(__METHOD__);

        try {
            // Make HTTP request to Groq API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->timeout(30)
                ->post($this->apiUrl, [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'model' => $this->model,
                    'temperature' => 0.7,
                    'max_completion_tokens' => $maxTokens,
                    'stream' => false,
                ]);

            // Process successful response
            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;

                return $this->cleanResponse($content);
            }

            // Log failed API attempt
            $this->logger->warning('Groq API attempt failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (Throwable $e) {
            // Log API error
            $this->logger->error('Groq API error', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Create a prompt for generating technology facts.
     *
     * @param  int  $count  Number of facts to generate
     * @return string Formatted prompt for the API
     */
    public function createPrompt(int $count = 10): string
    {
        return <<<PROMPT
            Generate $count different interesting facts about technology or internet.
            Each fact should be 1 sentence. Make them diverse and factual.
            Return as JSON array: ['fact1', 'fact2', ...]
            PROMPT;
    }

    /**
     * Generate an array of technology facts.
     *
     * @param  int  $count  Number of facts to generate
     * @return ?array Array of facts or null if failed
     */
    public function generateFactsArray(int $count = 10): ?array
    {
        // Set the current method as the caller for logging purposes
        $this->logger->setCaller(__METHOD__);

        $response = $this->callGroqApi($this->createPrompt($count), 500);

        if (!$response) {
            return null;
        }

        try {
            $facts = json_decode($response, true);

            // If it's a string, we try to find the JSON inside
            if (!is_array($facts)) {
                preg_match('/\[.*\]/s', $response, $matches);
                if (isset($matches[0])) {
                    $facts = json_decode($matches[0], true);
                }
            }

            if (is_array($facts)) {
                return array_slice(array_map('trim', $facts), 0, $count);
            }

        } catch (Throwable $e) {
            // Log JSON parsing error
            $this->logger->error('Failed to parse facts JSON', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Generate a single technology fact.
     *
     * @return ?string A single fact or null if failed
     */
    public function generateSingleFact(): ?string
    {
        $prompt = 'Generate one interesting fact about technology. One sentence only.';

        return $this->callGroqApi($prompt);
    }

    /**
     * Clean and format the API response.
     *
     * @param  ?string  $response  The raw response from API
     * @return ?string Cleaned response or null if input is null
     */
    protected function cleanResponse(?string $response): ?string
    {
        if (!$response) {
            return null;
        }

        // Remove markdown formatting
        $response = str_replace(['```json', '```', '**', '*', '`'], '', $response);

        // Normalize whitespace
        $response = preg_replace('/\s+/', ' ', trim($response));

        return trim($response);
    }

    /**
     * Test API connection.
     *
     * @return bool True if connection is successful
     */
    public function testConnection(): bool
    {
        $response = $this->callGroqApi('Say "OK" if you are working.', 10);

        return !empty($response) && stripos($response, 'OK') !== false;
    }
}
