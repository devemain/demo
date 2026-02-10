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

namespace App\Services\Logging;

use App\Services\Logging\Contracts\ChannelResolverInterface;
use App\Services\Logging\Contracts\LoggerInterface;
use App\Services\Logging\Contracts\MessageFormatterInterface;
use BadMethodCallException;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

/**
 * Handles logging operations with different log levels and channels.
 */
class LoggerService implements LoggerInterface
{
    /**
     * Array of allowed logging methods. This array is used to determine if a method call is valid.
     */
    private const array ALLOWED_METHODS = [
        'emergency', 'alert', 'critical',
        'error', 'warning', 'notice',
        'info', 'debug',
    ];

    /**
     * The current logging channel. This can be changed using the setChannel method.
     */
    protected string $channel = 'daily';

    /**
     * Creates a new instance.
     *
     * @param  ChannelResolverInterface  $channelResolver  Service for determining logging channels
     * @param  MessageFormatterInterface  $messageFormatter  Service for formatting log messages
     * @param  string|null  $fullMethod  Optional full method name (Class::method) to set as caller
     */
    public function __construct(
        protected readonly ChannelResolverInterface $channelResolver,
        protected readonly MessageFormatterInterface $messageFormatter,
        ?string $fullMethod = null
    ) {
        if ($fullMethod) {
            $this->setCaller($fullMethod);
        }
    }

    /**
     * Magic method to handle log level calls.
     *
     * @param  string  $method  The method name being called
     * @param  array  $args  The arguments passed to the method
     *
     * @throws BadMethodCallException If the method is not a valid logging method
     */
    public function __call(string $method, array $args): void
    {
        if (in_array($method, self::ALLOWED_METHODS, true)) {
            $this->write($method, ...$args);

            return;
        }

        throw new BadMethodCallException('Method ' . $method . ' does not exist');
    }

    /**
     * Set the caller information and determine the appropriate channel. This is used to prefix log messages.
     *
     * @param  string  $fullMethod  The full method name (Class::method)
     */
    public function setCaller(string $fullMethod): self
    {
        [$class, $method] = explode('::', $fullMethod, 2);

        $this->messageFormatter->setCaller($class, $method);
        $this->channel = $this->channelResolver->resolveChannel($class);

        return $this;
    }

    /**
     * Log an emergency message.
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    /**
     * Log an alert message.
     */
    public function alert(string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    /**
     * Log a critical message.
     */
    public function critical(string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    /**
     * Log an error message.
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /**
     * Log a warning message.
     */
    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    /**
     * Log a notice message.
     */
    public function notice(string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    /**
     * Log an info message.
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /**
     * Log a debug message.
     */
    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    /**
     * Set the logging channel.
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get the logger instance for the current channel.
     */
    public function log(): ?Logger
    {
        $logger = Log::channel($this->channel);

        return $logger instanceof Logger ? $logger : null;
    }

    /**
     * Write a log message with the specified level.
     *
     * @param  string  $level  The log level
     * @param  string  $message  The message to log
     * @param  array  $context  Additional context data
     */
    protected function write(string $level, string $message, array $context = []): void
    {
        $fullMessage = $this->messageFormatter->format($message);
        Log::channel($this->channel)->$level($fullMessage, $context);
    }
}
