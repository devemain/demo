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

namespace App\Repositories\Contracts;

use App\Models\Fact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Interface for fact repository operations.
 * This follows the Dependency Inversion Principle by abstracting
 * database operations behind an interface.
 */
interface FactRepositoryInterface
{
    /**
     * Find a fact by ID.
     *
     * @param int $id
     * @return Fact|null
     */
    public function findById(int $id): ?Fact;

    /**
     * Save facts to database with upsert operation.
     *
     * @param array $facts Array of facts to save
     * @return array Array of saved facts
     */
    public function saveFacts(array $facts): array;

    /**
     * Mark fact as shown and increment views.
     *
     * @param Fact $fact
     * @return bool
     */
    public function markAsShown(Fact $fact): bool;

    /**
     * Get the least shown fact (fair rotation).
     *
     * @return Fact|null
     */
    public function getFreshFact(): ?Fact;

    /**
     * Get a truly random fact.
     *
     * @return Fact|null
     */
    public function getRandomFact(): ?Fact;

    /**
     * Get multiple random facts.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRandomFacts(int $limit = 5): Collection;

    /**
     * Get recent facts limited by count.
     *
     * @param int $limit Maximum number of facts to return
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Get paginated facts ordered by latest.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Search facts by content.
     *
     * @param string $query Search query
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;

    /**
     * Check if any facts exist.
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Get total count of facts.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Get count of facts created on a specific date.
     *
     * @param Carbon $date Date object
     * @return int
     */
    public function countByDate(Carbon $date): int;

    /**
     * Get count of facts created in a specific month and year.
     *
     * @param Carbon $date Date object representing the month
     * @return int
     */
    public function countByMonth(Carbon $date): int;
}
