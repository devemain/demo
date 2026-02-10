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

namespace App\Services\Logging\Contracts;

/**
 * Interface for formatting log messages.
 * This follows the Single Responsibility Principle by separating
 * message formatting logic from logging operations.
 */
interface MessageFormatterInterface
{
    /**
     * Format a log message with caller information.
     *
     * @param  string  $message  The original message
     * @param  string|null  $caller  The caller information (Class::method)
     * @return string The formatted message
     */
    public function format(string $message, ?string $caller = null): string;

    /**
     * Set the caller information.
     *
     * @param  string  $class  The full class name (with namespace)
     * @param  string  $method  The method name
     */
    public function setCaller(string $class, string $method): self;

    /**
     * Set the separator between caller and message.
     *
     * @param  string  $separator  The separator string
     */
    public function setSeparator(string $separator): self;
}
