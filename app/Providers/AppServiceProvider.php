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

use App\Services\SafeUpsertService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /** @var Application $app */
        $app = $this->app;

        // Force HTTPS for all URLs for only production
        if ($app->isProduction()) {
            URL::forceScheme('https');
        }

        // Creating safeUpsert macros for Builder
        Builder::macro('safeUpsert', function (array $values, array $uniqueBy, array $update = null) {
            /** @var Builder $this */
            return SafeUpsertService::handle($this, $values, $uniqueBy, $update);
        });
    }
}
