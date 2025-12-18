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

class LoggerService
{
    private const ALLOWED_METHODS = [
        'emergency', 'alert', 'critical',
        'error', 'warning', 'notice',
        'info', 'debug'
    ];

    private string $caller = '';
    private string $channel = 'daily';
    private string $separator = ' --> ';

    public function __construct(?string $fullMethod = null)
    {
        if ($fullMethod) {
            $this->setCaller($fullMethod);
        }
    }

    /**
     * Magic method to handle log level calls
     */
    public function __call(string $method, array $args): void
    {
        if (in_array($method, self::ALLOWED_METHODS, true)) {
            $this->write($method, ...$args);
            return;
        }

        throw new BadMethodCallException('Method ' . $method . ' does not exist');
    }

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

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function log(): ?Logger
    {
        $logger = Log::channel($this->channel);
        return $logger instanceof Logger ? $logger : null;
    }

    protected function write(string $level, string $message, array $context = []): void
    {
        $fullMessage = $this->caller ? $this->caller . $this->separator . $message : $message;
        Log::channel($this->channel)->$level($fullMessage, $context);
    }

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
