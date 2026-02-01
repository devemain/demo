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

namespace App\Providers;

use App\Repositories\Contracts\FactRepositoryInterface;
use App\Repositories\FactRepository;
use App\Services\Fact\Contracts\AIServiceInterface;
use App\Services\Fact\Contracts\FallbackFactsProviderInterface;
use App\Services\Fact\FallbackFactsProvider;
use App\Services\Fact\GroqService;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for fact-related services.
 */
class FactServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind interfaces
        $this->app->bind(FactRepositoryInterface::class, FactRepository::class);
        $this->app->bind(FallbackFactsProviderInterface::class, FallbackFactsProvider::class);
        $this->app->bind(AIServiceInterface::class, GroqService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
