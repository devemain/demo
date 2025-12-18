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

namespace App\Http\Middleware\Api\Admin;

use App\Services\LoggerService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyCronSecret
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $logger = new LoggerService(__METHOD__);

        $secret = (string) $request->header('X-Cron-Secret');
        $configSecret = config('api.cron_secret');

        if (!$secret || !hash_equals($configSecret, $secret)) {
            $logger->warning('Unauthorized request from IP ' . $request->ip(), [
                'has_secret' => !empty($secret),
                'secret_match' => hash_equals($configSecret, $secret),
            ]);

            return response()->json([
                'error' => 'Invalid or missing cron secret'
            ], 401);
        }

        return $next($request);
    }
}
