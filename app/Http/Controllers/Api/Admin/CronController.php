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

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Fact\FactService;
use App\Services\LoggerService;
use App\Traits\Api\ErrorHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class CronController extends Controller
{
    use ErrorHandler;

    public function __construct(
        protected FactService $service,
        protected LoggerService $logger
    ) {}

    public function generateFacts(Request $request): JsonResponse
    {
        $this->logger->setCaller(__METHOD__);

        $this->logger->info('Triggered from IP ' . $request->ip(), [
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $generated = $this->service->generateFacts();

            $this->logger->info('Successful: ' . count($generated));

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
            return $this->handleApiError($e, 'Failed to generate facts');
        }
    }
}
