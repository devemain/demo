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

use App\Data\View\MenuItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

    protected function buildMainMenu(): void
    {
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
