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

namespace App\Services\Logging;

use App\Services\Logging\Contracts\MessageFormatterInterface;

/**
 * Formats log messages with caller information.
 * This follows the Single Responsibility Principle by focusing
 * solely on message formatting logic.
 */
class MessageFormatter implements MessageFormatterInterface
{
    /**
     * The caller information (Class::method).
     */
    protected string $caller = '';

    /**
     * The separator between caller and message.
     */
    protected string $separator = ' --> ';

    /**
     * Format a log message with caller information.
     *
     * @param string $message The original message
     * @param string|null $caller The caller information (Class::method)
     * @return string The formatted message
     */
    public function format(string $message, ?string $caller = null): string
    {
        $callerToUse = $caller ?? $this->caller;

        if (empty($callerToUse)) {
            return $message;
        }

        return $callerToUse . $this->separator . $message;
    }

    /**
     * Set the caller information.
     *
     * @param string $class The full class name (with namespace)
     * @param string $method The method name
     * @return self
     */
    public function setCaller(string $class, string $method): self
    {
        // Only class name without namespace
        $parts = explode('\\', $class);
        $className = end($parts);

        $this->caller = $className . '::' . $method;

        return $this;
    }

    /**
     * Set the separator between caller and message.
     *
     * @param string $separator The separator string
     * @return self
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }
}
