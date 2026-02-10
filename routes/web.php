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

use App\Http\Controllers\FactsController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return view('home');
});

// Facts
Route::prefix('facts')->as('facts.')->group(function () {
    Route::get('', [FactsController::class, 'index'])->name('index');
    Route::get('stats', [FactsController::class, 'stats'])->name('stats');
    Route::get('search', [FactsController::class, 'search'])->name('search');
});

// Test
Route::get('test', function () {
    $paths = [
        'storage' => is_writable(storage_path()),
        'storage/framework' => is_writable(storage_path('framework')),
        'storage/framework/views' => is_writable(storage_path('framework/views')),
        'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
    ];

    return view('test', ['paths' => $paths]);
});
