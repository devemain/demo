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

namespace App\Services\Fact;

use App\Models\Fact;
use App\Repositories\Contracts\FactRepositoryInterface;
use App\Services\Fact\Contracts\AIServiceInterface;
use App\Services\Fact\Contracts\FallbackFactsProviderInterface;

/**
 * Provides methods to generate, retrieve, and manage facts.
 */
class FactService
{
    /**
     * Creates a new instance.
     *
     * @param AIServiceInterface $aiService Service for AI-powered fact generation
     * @param FactRepositoryInterface $factRepository Repository for fact data operations
     * @param FallbackFactsProviderInterface $fallbackProvider Provider of fallback facts
     */
    public function __construct(
        protected readonly AIServiceInterface $aiService,
        protected readonly FactRepositoryInterface $factRepository,
        protected readonly FallbackFactsProviderInterface $fallbackProvider
    ) {}

    /**
     * Generate facts using AI or fallback to pre-defined facts.
     *
     * @param int $count Number of facts to generate (default: 10)
     * @return array Array of generated facts
     */
    public function generateFacts(int $count = 10): array
    {
        $factsArray = $this->aiService->generateFactsArray($count);

        if (empty($factsArray)) {
            return $this->createFallbackFacts($count);
        }

        $this->factRepository->saveFacts($factsArray);

        return $factsArray;
    }

    /**
     * Get the least shown fact (fair rotation).
     *
     * @return Fact|null
     */
    public function getFreshFact(): ?Fact
    {
        if (!$this->factRepository->exists()) {
            $this->createFallbackFacts(20);
        }

        return $this->factRepository->getFreshFact();
    }

    /**
     * Mark fact as shown and increment views.
     *
     * @param Fact $fact
     * @return bool
     */
    public function markAsShown(Fact $fact): bool
    {
        return $this->factRepository->markAsShown($fact);
    }

    /**
     * Create fallback facts when AI is unavailable.
     *
     * @param int $count Number of facts to create
     * @return array Array of fallback facts
     */
    protected function createFallbackFacts(int $count = 10): array
    {
        $fallbackFacts = $this->fallbackProvider->createFacts($count);
        $this->factRepository->saveFacts($fallbackFacts);

        return $fallbackFacts;
    }
}
