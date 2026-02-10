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

namespace Devemain;

/**
 * Cross-platform format constants.
 */
final class Fmt
{
    /**
     * System-dependent end of line character.
     */
    public const string EOL = PHP_EOL;

    /**
     * Double end of line (empty line).
     */
    public const string EOL2 = PHP_EOL . PHP_EOL;

    /**
     * Directory separator.
     */
    public const string DS = DIRECTORY_SEPARATOR;

    /**
     * Path separator.
     */
    public const string PS = PATH_SEPARATOR;

    /**
     * PHP opening tag.
     */
    public const string PHP_OPEN = '<?php';

    /**
     * Strict types declaration.
     */
    public const string STRICT_TYPES = 'declare(strict_types=1);';

    /**
     * Regex pattern for strict types declaration.
     */
    public const string STRICT_TYPES_PATTERN = 'declare\(strict_types=1\);\s*';

    /**
     * Get strict types declaration with line ending.
     */
    public static function strictTypes(): string
    {
        return self::STRICT_TYPES . self::EOL2;
    }
}
