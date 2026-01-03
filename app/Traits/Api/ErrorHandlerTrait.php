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

namespace App\Traits\Api;

use App\Services\LoggerService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Provides a standardized method to handle API errors across the application.
 * It includes error logging and formatted error responses.
 */
trait ErrorHandlerTrait
{
    /**
     * Handle API errors and return a formatted JSON response.
     *
     * @param Throwable $e The caught exception or error
     * @param string $context Optional context information about where the error occurred
     * @return JsonResponse A JSON response containing error details
     */
    protected function handleApiError(Throwable $e, string $context = ''): JsonResponse
    {
        // Create error message with optional context
        $message = ($context ? $context . ': ' : '') . $e->getMessage();

        // Get logger instance (use property if exists, otherwise create new)
        $logger = property_exists($this, 'logger') ? $this->logger : new LoggerService(__METHOD__);

        // Log error details
        $logger->error($message, $this->getErrorDetails($e));

        // Prepare base response array
        $response = [
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ];

        // Add detailed error information if debug mode is enabled
        if (config('app.debug')) {
            $response['error'] = $this->getErrorDetails($e);
        }

        // Return JSON response with 500 status code
        return response()->json($response, 500);
    }

    /**
     * Get error details array for exception.
     *
     * @param Throwable $e The exception to get details for
     * @return array Array containing error details
     */
    private function getErrorDetails(Throwable $e): array
    {
        return [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
    }
}
