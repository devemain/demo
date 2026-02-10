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

use Devemain\CliHelper;
use Devemain\ConfigDeployer;
use Devemain\CopyrightManager;
use Devemain\LicenseManager;
use Devemain\StrictTypesManager;

// Autoload classes
spl_autoload_register(function (string $class): void {
    $prefix = 'Devemain\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace([$prefix, '\\'], ['', '/'], $class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$cli = new CliHelper;
$cli->execute((array) $argv);

try {
    new LicenseManager($cli)->run();
    new CopyrightManager($cli)->run();
    new StrictTypesManager($cli)->run();
    new ConfigDeployer($cli)->run();

    $cli::frameBottom('green');
} catch (Exception $e) {
    $cli::error('Error: ' . $e->getMessage());
    exit(1);
}
