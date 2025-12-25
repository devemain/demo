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

use BadMethodCallException;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

/**
 * Handles logging operations with different log levels and channels.
 */
class LoggerService
{
    /**
     * Array of allowed logging methods. This array is used to determine if a method call is valid.
     */
    private const array ALLOWED_METHODS = [
        'emergency', 'alert', 'critical',
        'error', 'warning', 'notice',
        'info', 'debug'
    ];

    /**
     * The caller information (class::method). This is used to prefix log messages.
     */
    private string $caller = '';

    /**
     * The logging channel to use. Defaults to 'daily'. This can be changed using the setChannel method.
     */
    private string $channel = 'daily';

    /**
     * The separator between caller and message. Defaults to ' --> '. This can be changed using the setSeparator method.
     */
    private string $separator = ' --> ';

    /**
     * Creates a new instance.
     *
     * Initializes the logger with an optional full method name.
     * @param string|null $fullMethod Optional full method name (Class::method) to set as caller
     */
    public function __construct(?string $fullMethod = null)
    {
        if ($fullMethod) {
            $this->setCaller($fullMethod);
        }
    }

    /**
     * Magic method to handle log level calls.
     * If the method name is not in the ALLOWED_METHODS array, a BadMethodCallException is thrown.
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
     * @param string $fullMethod The full method name (Class::method)
     * @return self
     */
    public function setCaller(string $fullMethod): self
    {
        list($class, $method) = explode('::', $fullMethod, 2);

        // Only class name without namespace
        $parts = explode('\\', $class);
        $className = end($parts);

        $this->caller = $className . '::' . $method;
        $this->channel = $this->determineChannel($class);

        return $this;
    }

    /**
     * Set the message separator. This is used to separate the caller and message in log messages.
     *
     * @param string $separator The separator string
     * @return self
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Log an emergency message.
     *
     * @param string $message
     * @param array $context
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    /**
     * Log an alert message.
     *
     * @param string $message
     * @param array $context
     */
    public function alert(string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    /**
     * Log a critical message.
     *
     * @param string $message
     * @param array $context
     */
    public function critical(string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /**
     * Log a warning message.
     *
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    /**
     * Log a notice message.
     *
     * @param string $message
     * @param array $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    /**
     * Log an info message.
     *
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /**
     * Log a debug message.
     *
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    /**
     * Get the logger instance for the current channel.
     *
     * @return Logger|null
     */
    public function log(): ?Logger
    {
        $logger = Log::channel($this->channel);
        return $logger instanceof Logger ? $logger : null;
    }

    /**
     * Write a log message with the specified level.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    protected function write(string $level, string $message, array $context = []): void
    {
        $fullMessage = $this->caller ? $this->caller . $this->separator . $message : $message;
        Log::channel($this->channel)->$level($fullMessage, $context);
    }

    /**
     * Determine the logging channel based on the class namespace.
     *
     * @param string $class
     * @return string
     */
    protected function determineChannel(string $class): string
    {
        return match(true) {
            str_contains($class, '\Api\Admin\\') => 'api-admin',
            str_contains($class, '\Api\V1\\'), str_contains($class, '\Api\\') => 'api-v1',
            str_contains($class, '\Services\\') => 'service',
            default => 'daily'
        };
    }
}
