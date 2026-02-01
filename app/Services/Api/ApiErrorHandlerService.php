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

namespace App\Services\Api;

use App\Services\Logging\Contracts\LoggerInterface;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Provides a standardized method to handle API errors across the application.
 * It includes error logging and formatted error responses.
 */
class ApiErrorHandlerService
{
    /**
     * Default HTTP status code for errors.
     */
    protected int $defaultStatusCode = 500;

    /**
     * Handle API errors and return a formatted JSON response.
     *
     * @param Throwable $e The caught exception or error
     * @param LoggerInterface $logger Service for logging errors with current state
     * @param string $context Optional context information about where the error occurred
     * @param int|null $statusCode Optional HTTP status code (uses default if not provided)
     * @return JsonResponse A JSON response containing error details
     */
    public function handleError(Throwable $e, LoggerInterface $logger, string $context = '', ?int $statusCode = null): JsonResponse
    {
        // Create error message with optional context
        $message = ($context ? $context . ': ' : '') . $e->getMessage();

        // Log error details
        $logger->error($message, $this->getErrorDetails($e));

        // Prepare base response array
        $response = [
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : 'An error occurred',
        ];

        // Add detailed error information if debug mode is enabled
        if (config('app.debug')) {
            $response['error'] = $this->getErrorDetails($e);
        }

        // Determine the status code
        $statusCode = $statusCode ?? $this->defaultStatusCode;

        // Return JSON response with status code
        return response()->json($response, $statusCode);
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
