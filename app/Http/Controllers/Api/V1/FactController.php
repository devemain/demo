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
use App\Traits\Api\ErrorHandlerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Provides endpoints for retrieving facts,
 * either randomly or by specific ID. It uses services for
 * business logic and error handling.
 */
class FactController extends Controller
{
    use ErrorHandlerTrait;

    /**
     * Creates a new instance.
     *
     * @param FactService $factService Service responsible for generating facts
     * @param LoggerService $logger Service responsible for logging activities
     */
    public function __construct(
        protected readonly FactService $factService,
        protected readonly LoggerService $logger
    ) {}

    /**
     * GET /api/v1/fact
     *
     * Get random fact.
     *
     * This endpoint retrieves a random fact from the database.
     * If no facts are available, it returns a 404 response.
     * The fact is marked as shown after retrieval.
     *
     * @return JsonResponse JSON response containing the fact or error message
     */
    public function getRandomFact(): JsonResponse
    {
        // Set the current method as the caller for logging purposes
        $this->logger->setCaller(__METHOD__);

        try {
            // Attempt to retrieve a fresh fact
            $fact = $this->factService->getFreshFact();

            // Check if the retrieved fact is valid
            if (!$fact instanceof Fact) {
                // Log error and return 404 response if no facts available
                $this->logger->error('No facts available in database');

                return response()->json([
                    'success' => false,
                    'message' => 'No facts available in database',
                    'hint' => 'Run cron job to generate facts first'
                ], 404);
            }

            // Mark the fact as shown
            $this->factService->markAsShown($fact);

            // Return successful response with fact data and metadata
            return response()->json([
                'success' => true,
                'data' => $fact,
                'meta' => [
                    'total_facts' => Fact::query()->count(),
                    'timestamp' => Carbon::now()->toISOString(),
                ]
            ], options: JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            // Handle any unexpected errors
            return $this->handleApiError($e, 'Failed to get fact');
        }
    }

    /**
     * GET /api/v1/fact/{id}
     *
     * Get fact by ID.
     *
     * This endpoint retrieves a specific fact by its ID.
     * If the fact is not found, it returns a 404 response.
     * The fact is marked as shown after retrieval.
     *
     * @param int $id The ID of the fact to retrieve
     * @return JsonResponse JSON response containing the fact or error message
     */
    public function getFactById(int $id): JsonResponse
    {
        // Find the fact by ID
        $fact = Fact::query()->find($id);

        // Check if the fact exists
        if (!$fact instanceof Fact) {
            // Log error and return 404 response when fact is not found
            $this->logger->error('Fact not found: ' . $id);

            return response()->json([
                'success' => false,
                'message' => 'Fact not found'
            ], 404);
        }

        // Mark the fact as shown
        $this->factService->markAsShown($fact);

        // Return successful response with fact data
        return response()->json([
            'success' => true,
            'data' => $fact,
        ]);
    }
}
