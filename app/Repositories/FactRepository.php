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

namespace App\Repositories;

use App\Models\Fact;
use App\Repositories\Contracts\FactRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of the fact repository.
 */
class FactRepository implements FactRepositoryInterface
{
    /**
     * Find a fact by ID.
     *
     * @param  int  $id  Fact ID to search for
     * @return Fact|null Found fact or null if not found
     */
    public function findById(int $id): ?Fact
    {
        return Fact::query()->find($id);
    }

    /**
     * Save facts to database with upsert operation.
     *
     * @param  array  $facts  Array of facts to save
     * @return array Array of saved facts
     */
    public function saveFacts(array $facts): array
    {
        if (empty($facts)) {
            return [];
        }

        $now = Carbon::now();
        $prepared = array_map(
            fn (string $value): array => [
                'hash' => Fact::makeHash($value),
                'content' => trim($value),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $facts
        );

        return Fact::safeUpsert($prepared, ['hash'], ['content', 'updated_at']);
    }

    /**
     * Mark fact as shown and increment views.
     */
    public function markAsShown(Fact $fact): bool
    {
        $fact->views++;
        $fact->last_shown_at = Carbon::now();

        return $fact->save();
    }

    /**
     * Get the least shown fact (fair rotation).
     */
    public function getFreshFact(): ?Fact
    {
        $fact = Fact::query()
            ->orderBy('last_shown_at')
            ->orderBy('views')
            ->first();

        return $fact instanceof Fact ? $fact : null;
    }

    /**
     * Get a truly random fact.
     */
    public function getRandomFact(): ?Fact
    {
        $fact = Fact::query()->inRandomOrder()->first();

        return $fact instanceof Fact ? $fact : null;
    }

    /**
     * Get multiple random facts.
     */
    public function getRandomFacts(int $limit = 5): Collection
    {
        return Fact::query()
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent facts limited by count.
     *
     * @param  int  $limit  Maximum number of facts to return
     */
    public function getRecent(int $limit = 10): Collection
    {
        return Fact::query()
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get paginated facts ordered by latest.
     *
     * @param  int  $perPage  Number of items per page
     * @param  int  $page  Current page number
     */
    public function getPaginated(int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return Fact::query()
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Search facts by content.
     *
     * @param  string  $query  Search query
     * @param  int  $perPage  Number of items per page
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Fact::query()
            ->where('content', 'like', '%' . $query . '%')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Check if any facts exist.
     */
    public function exists(): bool
    {
        return Fact::query()->exists();
    }

    /**
     * Get total count of facts.
     */
    public function count(): int
    {
        return Fact::query()->count();
    }

    /**
     * Get count of facts created on a specific date.
     *
     * @param  Carbon  $date  Date object
     */
    public function countByDate(Carbon $date): int
    {
        return Fact::query()
            ->whereDate('created_at', $date)
            ->count();
    }

    /**
     * Get count of facts created in a specific month and year.
     *
     * @param  Carbon  $date  Date object representing the month
     */
    public function countByMonth(Carbon $date): int
    {
        return Fact::query()
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();
    }
}
