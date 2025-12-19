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
use Illuminate\Support\ServiceProvider;

class BuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->bootBuilderMacros();
    }

    protected function bootBuilderMacros(): void
    {
        // Creating safeUpsert macros for Builder
        Builder::macro('safeUpsert', function (array $values, array $uniqueBy, array $update = null) {
            /** @var Builder $this */
            return SafeUpsertService::handle($this, $values, $uniqueBy, $update);
        });
    }
}
