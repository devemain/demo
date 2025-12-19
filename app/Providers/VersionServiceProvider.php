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

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class VersionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('app.version', function () {
            return Cache::rememberForever('app_version', function () {
                $packageJsonPath = base_path('package.json');
                if (file_exists($packageJsonPath)) {
                    $packageJson = json_decode(file_get_contents($packageJsonPath), true);
                    return $packageJson['version'] ?? '1.0.0';
                }
                return '1.0.0';
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        config(['app.version' => $this->app->make('app.version')]);
    }
}
