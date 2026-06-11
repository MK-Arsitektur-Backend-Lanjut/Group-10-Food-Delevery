<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOrderItemsRequest;
use App\Services\MenuValidationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class InternalIntegrationController extends Controller
{
    public function __construct(protected MenuValidationService $validationService)
    {
    }

    #[OA\Post(
        path: '/internal/order-items/validate',
        summary: 'Validate requested order items',
        description: 'Internal endpoint to validate if the restaurant is open and if all requested menu items are available. Combines duplicates inside payload automatically.',
        tags: ['Internal Integration'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ValidateOrderItemsRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Validation passed successfully (or normalized successfully)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'valid', type: 'boolean', example: true),
                        new OA\Property(property: 'restaurant', type: 'object'),
                        new OA\Property(property: 'items', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed (e.g., restaurant closed, item unavailable)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'valid', type: 'boolean', example: false),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function validateOrderItems(ValidateOrderItemsRequest $request): JsonResponse
    {
        $result = $this->validationService->validate(
            $request->validated('restaurant_id'),
            $request->validated('items')
        );

        // Keep root HTTP response as 200, but structured JSON output acts as contract
        return response()->json($result, $result['valid'] ? 200 : 422);
    }
}
