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

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SafeUpsertService
{
    public static function handle(Model|Builder $model, array $values, array $uniqueBy, array $update = null): array
    {
        if (empty($values)) {
            return ['inserted' => 0, 'updated' => 0];
        }

        $model = $model instanceof Builder ? $model->getModel() : $model;

        $uniqueKey = $uniqueBy[0];
        $uniqueValues = array_column($values, $uniqueKey);

        $existing = $model->newQuery()
            ->whereIn($uniqueKey, $uniqueValues)
            ->pluck($uniqueKey)
            ->toArray();

        $toInsert = array_filter($values, fn($v) => !in_array($v[$uniqueKey], $existing));
        $toUpdate = array_filter($values, fn($v) => in_array($v[$uniqueKey], $existing));

        $inserted = !empty($toInsert) && $model->newQuery()->insert($toInsert) ? count($toInsert) : 0;

        $updated = 0;
        if (!empty($toUpdate) && !empty($update)) {
            foreach ($toUpdate as $item) {
                $updateData = Arr::only($item, $update);
                if (!empty($updateData)) {
                    $model->newQuery()
                        ->where($uniqueKey, $item[$uniqueKey])
                        ->update($updateData);
                    $updated++;
                }
            }
        }

        return compact('inserted', 'updated');
    }
}
