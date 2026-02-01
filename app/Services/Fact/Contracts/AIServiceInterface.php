<?php
/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Services\Fact\Contracts;

/**
 * Interface for AI-powered fact generation services.
 * Implementations can use different AI providers (Groq, OpenAI, etc.)
 */
interface AIServiceInterface
{
    /**
     * Generate an array of facts.
     *
     * @param int $count Number of facts to generate
     * @return ?array Array of facts or null if failed
     */
    public function generateFactsArray(int $count = 10): ?array;

    /**
     * Generate a single fact.
     *
     * @return ?string A single fact or null if failed
     */
    public function generateSingleFact(): ?string;

    /**
     * Test the AI service connection.
     *
     * @return bool True if connection is successful
     */
    public function testConnection(): bool;
}
