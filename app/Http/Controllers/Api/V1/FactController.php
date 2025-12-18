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

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fact;
use App\Services\Fact\FactService;
use App\Services\LoggerService;
use App\Traits\Api\ErrorHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Throwable;

class FactController extends Controller
{
    use ErrorHandler;

    public function __construct(
        protected FactService $factService,
        protected LoggerService $logger
    ) {}

    /**
     * Get random fact
     * GET /api/v1/fact
     */
    public function getRandomFact(): JsonResponse
    {
        $this->logger->setCaller(__METHOD__);

        try {
            $fact = $this->factService->getFreshFact();

            if (!$fact instanceof Fact) {
                return response()->json([
                    'success' => false,
                    'message' => 'No facts available in database',
                    'hint' => 'Run cron job to generate facts first'
                ], 404);
            }

            $this->factService->markAsShown($fact);

            return response()->json([
                'success' => true,
                'data' => $fact,
                'meta' => [
                    'total_facts' => Fact::query()->count(),
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ], options: JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return $this->handleApiError($e, 'Failed to get fact');
        }
    }

    /**
     * Get fact by ID
     * GET /api/v1/fact/{id}
     */
    public function getFactById(int $id): JsonResponse
    {
        $fact = Fact::query()->find($id);

        if (!$fact instanceof Fact) {
            return response()->json([
                'success' => false,
                'message' => 'Fact not found'
            ], 404);
        }

        $this->factService->markAsShown($fact);

        return response()->json([
            'success' => true,
            'data' => $fact,
        ]);
    }
}
