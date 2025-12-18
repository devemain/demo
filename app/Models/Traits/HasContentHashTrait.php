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

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * @mixin Model
 */
trait HasContentHashTrait
{
    protected static function getAllowedHashAlgorithms(): array
    {
        return ['md5', 'sha256', 'sha512'];
    }

    public static function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Remove punctuation marks
        $text = preg_replace('/[^\w\s]/u', '', $text);

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    public static function makeHash(string $content, string $algo = 'md5'): string
    {
        $algorithms = static::getAllowedHashAlgorithms();

        if (!in_array($algo, $algorithms, strict: true)) {
            throw new RuntimeException(
                sprintf(
                    'Hash algorithm "%s" is not allowed. Allowed: %s',
                    $algo,
                    implode(', ', $algorithms)
                )
            );
        }

        $normalized = static::normalizeText($content);

        if ($normalized === '') {
            throw new RuntimeException('Cannot hash empty content');
        }

        return hash($algo, $normalized);
    }

    protected static function bootHasContentHashTrait(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->hash)) {
                $model->hash = static::makeHash($model->content);
            }
        });

        static::updating(function (Model $model) {
            if ($model->isDirty('content')) {
                $model->hash = static::makeHash($model->content);
            }
        });
    }
}
