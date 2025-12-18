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
use App\Http\Controllers\Api\V1\FactController;
use App\Http\Controllers\Api\Admin\CronController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('fact', [FactController::class, 'getRandomFact']);
    Route::get('fact/{id}', [FactController::class, 'getFactById']);
});

Route::prefix('cron')->group(function () {
    Route::post('generate-facts', [CronController::class, 'generateFacts'])->middleware('api.cron_secret');
    Route::get('generate-facts', [CronController::class, 'generateFacts']);
//        ->middleware('api.cron_secret');
});
