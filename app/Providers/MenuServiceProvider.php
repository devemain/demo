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

use App\Data\View\MenuItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Provider is responsible for registering and booting
 * the menu-related services for the application.
 */
class MenuServiceProvider extends ServiceProvider
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
        $this->buildMainMenu();
    }

    /**
     * Build the main navigation menu.
     *
     * This method creates an array of menu items with their respective
     * URIs, titles, icons, and active states. It then shares this menu
     * with the '_layouts.app' view.
     */
    protected function buildMainMenu(): void
    {
        // Define the menu items array
        $menu = [
            MenuItem::make(
                uri: '/',
                title: 'Home',
                icon: 'fas fa-home',
                active: request()->is('/')
            ),
            MenuItem::make(
                uri: '/facts',
                title: 'Tech Facts',
                icon: 'fas fa-rocket',
                active: request()->is(['facts', 'facts/search'])
            ),
            MenuItem::make(
                uri: '/facts/stats',
                title: 'Statistics',
                icon: 'fas fa-chart-line',
                active: request()->is('facts/stats')
            ),
        ];

        View::composer('_layouts.app', function ($view) use ($menu) {
            $view->with('menu', $menu);
        });
    }
}
