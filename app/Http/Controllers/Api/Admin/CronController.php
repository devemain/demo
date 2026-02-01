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

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Api\ApiErrorHandlerService;
use App\Services\Fact\FactService;
use App\Services\Logging\Contracts\LoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Handles cron job related operations, specifically for generating facts.
 */
class CronController extends Controller
{
    /**
     * Creates a new instance.
     *
     * @param FactService $factService Service responsible for generating facts
     * @param LoggerInterface $logger Service responsible for logging activities
     * @param ApiErrorHandlerService $errorHandler Service responsible for handling API errors
     */
    public function __construct(
        protected readonly FactService $factService,
        protected readonly LoggerInterface $logger,
        protected readonly ApiErrorHandlerService $errorHandler
    ) {}

    /**
     * Generate facts endpoint.
     *
     * This method handles the request to generate facts. It logs the request details,
     * attempts to generate facts, and returns a JSON response with the results.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response containing either the generated facts or an error message
     */
    public function generateFacts(Request $request): JsonResponse
    {
        // Set the current method as the caller for logging purposes
        $this->logger->setCaller(__METHOD__);

        // Log the request information including IP and user agent
        $this->logger->info('Triggered from IP ' . $request->ip(), [
            'user_agent' => $request->userAgent(),
        ]);

        try {
            // Generate facts using the injected service
            $generated = $this->factService->generateFacts();

            // Log the successful generation count
            $this->logger->info('Successful: ' . count($generated));

            // Return a successful JSON response with the generated facts
            return response()->json([
                'success' => true,
                'message' => 'Facts generated successfully',
                'data' => [
                    'count' => count($generated),
                    'facts' => $generated,
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ]);

        } catch (Throwable $e) {
            // Handle any unexpected errors using centralized error handler
            return $this->errorHandler->handleError($e, $this->logger, 'Failed to generate facts');
        }
    }
}
