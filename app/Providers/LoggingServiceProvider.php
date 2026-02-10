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

namespace App\Providers;

use App\Services\Logging\ChannelResolver;
use App\Services\Logging\Contracts\ChannelResolverInterface;
use App\Services\Logging\Contracts\LoggerInterface;
use App\Services\Logging\Contracts\MessageFormatterInterface;
use App\Services\Logging\LoggerService;
use App\Services\Logging\MessageFormatter;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for logging and fact selection services.
 */
class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces
        $this->app->bind(LoggerInterface::class, LoggerService::class);
        $this->app->singleton(ChannelResolverInterface::class, ChannelResolver::class);
        $this->app->singleton(MessageFormatterInterface::class, MessageFormatter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
