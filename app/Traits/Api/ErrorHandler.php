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

namespace App\Traits\Api;

use App\Services\LoggerService;
use Illuminate\Http\JsonResponse;
use Throwable;

trait ErrorHandler
{
    protected function handleApiError(Throwable $e, string $context = ''): JsonResponse
    {
        $message = ($context ? $context . ': ' : '') . $e->getMessage();

        $logger = property_exists($this, 'logger') ? $this->logger : new LoggerService(__METHOD__);
        $logger->error($message, [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        $response = [
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ];

        if (config('app.debug')) {
            $response['error'] = [
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }

        return response()->json($response, 500);
    }
}
