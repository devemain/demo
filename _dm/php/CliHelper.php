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

namespace Devemain;

use Devemain\Traits\CommandOptionsTrait;

/**
 * Provides helper methods for command line interface operations.
 * It includes functionality for colored output, framing text, and displaying help information.
 */
class CliHelper
{
    use CommandOptionsTrait;

    /**
     * Constant for double line break (two PHP_EOLs).
     */
    public const string PHP_EOL2 = PHP_EOL . PHP_EOL;

    /**
     * Constants for CLI color codes.
     * Contains various ANSI color codes for terminal output formatting.
     */
    private const array CLI_COLORS = [
        'red' => "\e[0;31m",
        'red_underline' => "\e[4;31m",
        'green' => "\e[0;32m",
        'green_underline' => "\e[4;32m",
        'yellow' => "\e[0;33m",
        'yellow_underline' => "\e[4;33m",
        'blue' => "\e[0;34m",
        'blue_underline' => "\e[4;34m",
        'magenta' => "\e[0;35m",
        'magenta_underline' => "\e[4;35m",
        'cyan' => "\e[0;36m",
        'cyan_underline' => "\e[4;36m",
        'white' => "\e[0;37m",
        'white_underline' => "\e[4;37m",
        'reset_color' => "\e[0m",
    ];

    /**
     * Cache for color support detection.
     */
    private static ?bool $supportsColor = null;

    /**
     * Apply color to text if color is supported. Otherwise, return original text.
     *
     * @param string $text The text to color
     * @param string|null $color The color to apply
     * @return string The colored text or original text if color is not supported
     */
    private static function color(string $text, ?string $color = null): string
    {
        if (!$color || !isset(self::CLI_COLORS[$color])) {
            return $text;
        }

        // Check if terminal supports color
        if (self::$supportsColor === null) {
            self::$supportsColor =
                (function_exists('posix_isatty') && posix_isatty(STDOUT)) ||
                getenv('ANSICON') !== false ||
                getenv('ConEmuANSI') === 'ON' ||
                (defined('STDOUT') && stream_isatty(STDOUT));
        }

        if (self::$supportsColor) {
            return self::CLI_COLORS[$color] . $text . self::CLI_COLORS['reset_color'];
        }

        return $text;
    }

    /**
     * Print colored text to console. If color is not supported, print plain text.
     *
     * @param string $text The text to print
     * @param string $color The color to use
     * @param bool $frame Whether to frame the text
     * @param string|null $icon Icon to display before text
     */
    public static function print(string $text, string $color = 'white', bool $frame = false, ?string $icon = null): void
    {
        if ($frame) {
            self::frameMiddle($text, $color, $icon);
        } else {
            echo self::color(($icon ? $icon . ' ' : '') . $text, $color) . PHP_EOL;
        }
    }

    /**
     * Print error message in red.
     *
     * @param string $text The error message
     * @param bool $frame Whether to frame the text
     */
    public static function error(string $text, bool $frame = false): void
    {
        self::print($text, 'red', $frame, '✘');
    }

    /**
     * Print success message in green.
     *
     * @param string $text The success message
     * @param bool $frame Whether to frame the text
     */
    public static function success(string $text, bool $frame = false): void
    {
        self::print($text, 'green', $frame, '✔');
    }

    /**
     * Print warning message in yellow.
     *
     * @param string $text The warning message
     * @param bool $frame Whether to frame the text
     */
    public static function warning(string $text, bool $frame = false): void
    {
        self::print($text, 'yellow', $frame, '⚠');
    }

    /**
     * Print info message in blue.
     *
     * @param string $text The info message
     * @param bool $frame Whether to frame the text
     */
    public static function info(string $text, bool $frame = false): void
    {
        self::print($text, 'blue', $frame, 'ℹ');
    }

    /**
     * Print loading message in magenta.
     *
     * @param string $text The loading message
     * @param bool $frame Whether to frame the text
     */
    public static function loading(string $text, bool $frame = false): void
    {
        self::print($text, 'magenta', $frame, '⟳');
    }

    /**
     * Print framed text.
     *
     * @param string $text The text to frame
     * @param string $color The color to use
     * @param string|null $icon Icon to display in the frame
     */
    public static function frame(string $text, string $color = 'cyan', ?string $icon = null): void
    {
        self::frameTop($color);
        self::frameMiddle($text, $color, $icon);
        self::frameBottom($color);
    }

    /**
     * Print top frame border.
     *
     * @param string $color The color to use
     */
    public static function frameTop(string $color = 'cyan'): void
    {
        self::print('┌─────────────────────────────────────────────────────────────────────────────────┐', $color);
    }

    /**
     * Print middle frame with text.
     *
     * @param string $text The text to display
     * @param string $color The color to use
     * @param string|null $icon Icon to display before text
     */
    public static function frameMiddle(string $text, string $color = 'white', ?string $icon = null): void
    {
        $text = $icon ? $icon . ' ' . $text : $text;
        $length = $icon ? 76 : 74;

        echo sprintf(self::color('│   %-' . $length . 's    │', $color), $text) . PHP_EOL;
    }

    /**
     * Print bottom frame border.
     *
     * @param string $color The color to use
     */
    public static function frameBottom(string $color = 'cyan'): void
    {
        self::print('└─────────────────────────────────────────────────────────────────────────────────┘', $color);
    }

    /**
     * Print separator line.
     *
     * @param string $color The color to use
     */
    public static function separator(string $color = 'white'): void
    {
        self::print('-----------------------------------------------------------------------------------', $color);
    }

    /**
     * Print directory name in blue.
     *
     * @param string $dir The directory path
     * @param bool $frame Whether to frame the text
     */
    public static function dir(string $dir, bool $frame = false): void
    {
        self::print('[DIR] ' . $dir . '/', 'blue', $frame);
    }

    /**
     * Print file name with extension.
     *
     * @param string $file The file path
     * @param bool $frame Whether to frame the text
     */
    public static function file(string $file, bool $frame = false): void
    {
        $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        self::print(str_pad('[' . ($extension ?: 'FILE') . ']', 10) . $file, frame: $frame);
    }

    /**
     * Display help information.
     *
     * @param string $script The script name
     */
    public static function showHelp(string $script): void
    {
        self::frame('Usage: php ' . $script . ' [OPTIONS] COMMAND [ARGUMENTS]');
        self::info('OPTIONS:', true);
        self::frameMiddle('(no options)     - Add or update copyright');
        self::frameMiddle('-h, --help       - Show this help');
        self::frameMiddle('-r, --remove     - Remove copyrights');
        self::frameBottom();
    }

    /**
     * Execute command line operations.
     *
     * @param array $argv Command line arguments
     */
    public function execute(array $argv): void
    {
        $this->parseCommandLine($argv);

        if ($this->hasOption('help')) {
            $this->showHelp($this->getScriptName());
            exit(0);
        }
    }
}
