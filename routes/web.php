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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/test', function() {
    $paths = [
        'storage' => is_writable(storage_path()),
        'storage/framework' => is_writable(storage_path('framework')),
        'storage/framework/views' => is_writable(storage_path('framework/views')),
        'bootstrap/cache' => is_writable(base_path('bootstrap/cache'))
    ];

    return view('test', ['paths' => $paths]);
});
