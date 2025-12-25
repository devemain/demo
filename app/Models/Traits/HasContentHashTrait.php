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
 * Handling content hashing functionality.
 * Provides methods to generate and manage content hashes.
 *
 * @mixin Model
 */
trait HasContentHashTrait
{
    /**
     * Get allowed hash algorithms.
     *
     * @return array List of allowed hash algorithm names
     */
    protected static function getAllowedHashAlgorithms(): array
    {
        return ['md5', 'sha256', 'sha512'];
    }

    /**
     * Normalize text before hashing.
     * Converts to lowercase, removes punctuation, and normalizes whitespace.
     *
     * @param string $text The text to normalize
     * @return string The normalized text
     */
    public static function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Remove punctuation marks
        $text = preg_replace('/[^\w\s]/u', '', $text);

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Generate a hash of the given content.
     *
     * @param string $content The content to hash
     * @param string $algo The hash algorithm to use (default: 'md5')
     * @return string The generated hash
     * @throws RuntimeException If algorithm is not allowed or content is empty
     */
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

    /**
     * Boot the trait.
     * Sets up event listeners to automatically update hash when content changes.
     */
    protected static function bootHasContentHashTrait(): void
    {
        // Set hash when creating new model if not already set
        static::creating(function (Model $model) {
            if (empty($model->hash)) {
                $model->hash = static::makeHash($model->content);
            }
        });

        // Update hash when content is modified
        static::updating(function (Model $model) {
            if ($model->isDirty('content')) {
                $model->hash = static::makeHash($model->content);
            }
        });
    }
}
