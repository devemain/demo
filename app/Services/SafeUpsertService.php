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

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Safely handling upsert operations (insert or update)
 * on database models while maintaining data integrity.
 */
class SafeUpsertService
{
    /**
     * Handle the upsert operation for the given model and values.
     *
     * @param  Model|Builder  $model  The Eloquent model or query builder instance
     * @param  array  $values  Array of data to be inserted or updated
     * @param  array  $uniqueBy  Column(s) to determine uniqueness
     * @param  array|null  $update  Columns to be updated when a match is found
     * @return array Array containing counts of inserted and updated records
     */
    public static function handle(Model|Builder $model, array $values, array $uniqueBy, ?array $update = null): array
    {
        // Return zero values if there's no data to process
        if (empty($values)) {
            return ['inserted' => 0, 'updated' => 0];
        }

        // Get the model instance if a builder was passed
        $model = $model instanceof Builder ? $model->getModel() : $model;

        // Extract unique key and values from input data
        $uniqueKey = $uniqueBy[0];
        $uniqueValues = array_column($values, $uniqueKey);

        // Find existing records with matching unique values
        $existing = $model->newQuery()
            ->whereIn($uniqueKey, $uniqueValues)
            ->pluck($uniqueKey)
            ->toArray();

        // Separate records into those to insert and those to update
        $toInsert = array_filter($values, fn ($v) => !in_array($v[$uniqueKey], $existing));
        $toUpdate = array_filter($values, fn ($v) => in_array($v[$uniqueKey], $existing));

        // Insert new records and count how many were inserted
        $inserted = !empty($toInsert) && $model->newQuery()->insert($toInsert) ? count($toInsert) : 0;

        // Update existing records if update fields are specified
        $updated = 0;
        if (!empty($toUpdate) && !empty($update)) {
            foreach ($toUpdate as $item) {
                // Prepare only the fields that should be updated
                $updateData = Arr::only($item, $update);
                if (!empty($updateData)) {
                    // Perform the update operation
                    $model->newQuery()
                        ->where($uniqueKey, $item[$uniqueKey])
                        ->update($updateData);
                    $updated++;
                }
            }
        }

        // Return the counts of inserted and updated records
        return compact('inserted', 'updated');
    }
}
