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
 * Interface for providing fallback facts.
 * This follows the Interface Segregation Principle by separating
 * the responsibility of providing fallback data from other services.
 */
interface FallbackFactsProviderInterface
{
    /**
     * Get fallback facts based on language.
     *
     * @param string $language Language code for facts (default: 'en')
     * @return array Array of fallback facts
     */
    public function getFacts(string $language = 'en'): array;

    /**
     * Create a specific number of fallback facts.
     *
     * @param int $count Number of facts to create
     * @return array Array of fallback facts
     */
    public function createFacts(int $count = 10): array;
}
