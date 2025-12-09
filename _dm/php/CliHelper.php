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

namespace Devemain;

use Devemain\Traits\CommandOptionsTrait;

class CliHelper
{
    use CommandOptionsTrait;

    public const PHP_EOL2 = PHP_EOL . PHP_EOL;

    private const CLI_COLORS = [
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

    private static ?bool $supportsColor = null;

    private static function color(string $text, ?string $color = null): string
    {
        if (!$color || !isset(self::CLI_COLORS[$color])) {
            return $text;
        }

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

    public static function print(string $text, string $color = 'white', bool $frame = false, ?string $icon = null): void
    {
        if ($frame) {
            self::frameMiddle($text, $color, $icon);
        } else {
            echo self::color(($icon ? $icon . ' ' : '') . $text, $color) . PHP_EOL;
        }
    }

    public static function error(string $text, bool $frame = false): void
    {
        self::print($text, 'red', $frame, '✘');
    }

    public static function success(string $text, bool $frame = false): void
    {
        self::print($text, 'green', $frame, '✔');
    }

    public static function warning(string $text, bool $frame = false): void
    {
        self::print($text, 'yellow', $frame, '⚠');
    }

    public static function info(string $text, bool $frame = false): void
    {
        self::print($text, 'blue', $frame, 'ℹ');
    }

    public static function loading(string $text, bool $frame = false): void
    {
        self::print($text, 'magenta', $frame, '⟳');
    }

    public static function frame(string $text, string $color = 'cyan', ?string $icon = null): void
    {
        self::frameTop($color);
        self::frameMiddle($text, $color, $icon);
        self::frameBottom($color);
    }

    public static function frameTop(string $color = 'cyan'): void
    {
        self::print('┌─────────────────────────────────────────────────────────────────────────────────┐', $color);
    }

    public static function frameMiddle(string $text, string $color = 'white', ?string $icon = null): void
    {
        $text = $icon ? $icon . ' ' . $text : $text;
        $length = $icon ? 76 : 74;

        echo sprintf(self::color('│   %-' . $length . 's    │', $color), $text) . PHP_EOL;
    }

    public static function frameBottom(string $color = 'cyan'): void
    {
        self::print('└─────────────────────────────────────────────────────────────────────────────────┘', $color);
    }

    public static function separator(string $color = 'white'): void
    {
        self::print('-----------------------------------------------------------------------------------', $color);
    }

    public static function dir(string $dir, bool $frame = false): void
    {
        self::print('[DIR] ' . $dir . '/', 'blue', $frame);
    }

    public static function file(string $file, bool $frame = false): void
    {
        $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        self::print(str_pad('[' . ($extension ?: 'FILE') . ']', 10) . $file, frame: $frame);
    }

    public static function showHelp(string $script): void
    {
        self::frame('Usage: php ' . $script . ' [OPTIONS] COMMAND [ARGUMENTS]');
        self::info('OPTIONS:', true);
        self::frameMiddle('(no options)     - Add or update copyright');
        self::frameMiddle('-h, --help       - Show this help');
        self::frameMiddle('-r, --remove     - Remove copyrights');
        self::frameBottom();
    }

    public function execute(array $argv): void
    {
        $this->parseCommandLine($argv);

        if ($this->hasOption('help')) {
            $this->showHelp($this->getScriptName());
            exit(0);
        }
    }
}
