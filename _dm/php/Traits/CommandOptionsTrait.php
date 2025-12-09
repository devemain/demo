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

namespace Devemain\Traits;

trait CommandOptionsTrait
{
    private string $scriptName = '';
    private array $options = [];

    protected function mapArgumentToOption(string $arg): ?string
    {
        return match ($arg) {
            '-h', '--help' => 'help',
            '-r', '--remove' => 'remove',
            '-f', '--force' => 'force',
            default => null,
        };
    }

    final public function parseCommandLine(array $argv): void
    {
        $this->scriptName = $argv[0] ?? '';
        $this->options = [];

        foreach (array_slice($argv, 1) as $arg) {
            $option = $this->mapArgumentToOption($arg);
            if ($option !== null) {
                $this->options[$option] = true;
            }
        }
    }

    final public function getScriptName(): string
    {
        return $this->scriptName;
    }

    final public function hasOption(string $name): bool
    {
        return isset($this->options[$name]) && $this->options[$name];
    }

    final public function getOption(string $name, $default = false)
    {
        return $this->options[$name] ?? $default;
    }

    final public function getOptions(): array
    {
        return $this->options;
    }
}
