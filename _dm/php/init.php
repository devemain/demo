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

use Devemain\CliHelper;
use Devemain\ConfigDeployer;
use Devemain\CopyrightManager;
use Devemain\LicenseManager;

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'Devemain\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace([$prefix, '\\'], ['', '/'], $class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$cli = new CliHelper;
$cli->execute($argv);

try {
    new LicenseManager($cli)->run();
    new CopyrightManager($cli)->run();
    new ConfigDeployer($cli)->run();
    $cli::frameBottom('green');
} catch (Exception $e) {
    $cli::error('Error: ' . $e->getMessage());
    exit(1);
}
