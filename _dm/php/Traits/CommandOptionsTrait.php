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

/**
 * Provides functionality for parsing and managing command line options
 * for CLI applications. It allows mapping command line arguments to named options
 * and provides methods to check for and retrieve these options.
 */
trait CommandOptionsTrait
{
    /**
     * Stores the name of the script/program.
     */
    private string $scriptName = '';
    
    /**
     * Stores the parsed command line options.
     */
    private array $options = [];

    /**
     * Maps a command line argument to a named option.
     *
     * @param string $arg The command line argument to map     *
     * @return string|null The name of the option if it's recognized, null otherwise
     */
    protected function mapArgumentToOption(string $arg): ?string
    {
        return match ($arg) {
            '-h', '--help' => 'help',
            '-r', '--remove' => 'remove',
            '-f', '--force' => 'force',
            default => null,
        };
    }

    /**
     * Parses command line arguments.
     *
     * @param array $argv The command line arguments array (typically $argv from PHP)
     */
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

    /**
     * Gets the script name.
     *
     * @return string The name of the script
     */
    final public function getScriptName(): string
    {
        return $this->scriptName;
    }

    /**
     * Checks if a specific option is set.
     *
     * @param string $name The name of the option to check
     * @return bool True if the option is set and true, false otherwise
     */
    final public function hasOption(string $name): bool
    {
        return isset($this->options[$name]) && $this->options[$name];
    }

    /**
     * Gets the value of a specific option.
     *
     * @param string $name The name of the option to retrieve
     * @param bool $default The default value to return if the option is not set
     * @return mixed The value of the option or the default value
     */
    final public function getOption(string $name, bool $default = false): mixed
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Gets all parsed options.
     *
     * @return array An array of all options
     */
    final public function getOptions(): array
    {
        return $this->options;
    }
}
