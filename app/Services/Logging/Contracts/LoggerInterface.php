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
 * Interface for logging operations.
 * This follows the Interface Segregation Principle by defining
 * only the essential logging methods.
 */
interface LoggerInterface
{
    /**
     * Log an emergency message.
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * Log an alert message.
     */
    public function alert(string $message, array $context = []): void;

    /**
     * Log a critical message.
     */
    public function critical(string $message, array $context = []): void;

    /**
     * Log an error message.
     */
    public function error(string $message, array $context = []): void;

    /**
     * Log a warning message.
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Log a notice message.
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Log an info message.
     */
    public function info(string $message, array $context = []): void;

    /**
     * Log a debug message.
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Set the logging channel.
     */
    public function setChannel(string $channel): self;
}
