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

/**
 * Responsible for verifying the cron secret header in incoming requests.
 * It ensures that only authorized cron jobs can access protected endpoints by comparing
 * the provided secret with the configured secret.
 */
class VerifyCronSecret
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming HTTP request instance
     * @param Closure $next The next middleware in the pipeline
     * @return JsonResponse The response after processing the request
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        // Initialize logger with current method name for tracking
        $logger = new LoggerService(__METHOD__);

        // Retrieve the secret from the request header and convert to string
        $secret = (string) $request->header('X-Cron-Secret');

        // Get the configured secret from the application configuration
        $configSecret = config('api.cron_secret');

        // Check if the secret is missing or doesn't match the configured secret
        if (!$secret || !hash_equals($configSecret, $secret)) {
            // Log the unauthorized attempt with relevant details
            $logger->warning('Unauthorized request from IP ' . $request->ip(), [
                'has_secret' => !empty($secret),
                'secret_match' => hash_equals($configSecret, $secret),
            ]);

            // Return a 401 Unauthorized response with error message
            return response()->json([
                'error' => 'Invalid or missing cron secret'
            ], 401);
        }

        // If secret is valid, proceed to the next middleware/request handler
        return $next($request);
    }
}
