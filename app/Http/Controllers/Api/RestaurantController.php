<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantOperationalStatusRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Services\RestaurantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RestaurantController extends Controller
{
    use ApiResponseHelper;

    public function __construct(protected RestaurantService $restaurantService)
    {
    }

    #[OA\Get(
        path: '/restaurants',
        summary: 'Get list of restaurants',
        tags: ['Restaurants'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->list($request->all(), $request->input('per_page', 15));
        
        return response()->json([
            'success' => true,
            'message' => 'Restaurants retrieved successfully',
            'data' => RestaurantResource::collection($restaurants),
            'meta' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total()
            ]
        ]);
    }

    #[OA\Post(
        path: '/restaurants',
        summary: 'Create a new restaurant',
        tags: ['Restaurants'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Restaurant')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Restaurant created successfully')
        ]
    )]
    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $restaurant = $this->restaurantService->create($request->validated());

        return $this->successResponse(
            new RestaurantResource($restaurant),
            'Restaurant created successfully',
            201
        );
    }

    #[OA\Get(
        path: '/restaurants/{restaurant}',
        summary: 'Get restaurant details',
        tags: ['Restaurants'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->findOrFail($id);

        return $this->successResponse(
            new RestaurantResource($restaurant),
            'Restaurant retrieved successfully'
        );
    }

    #[OA\Put(
        path: '/restaurants/{restaurant}',
        summary: 'Update an existing restaurant',
        tags: ['Restaurants'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Restaurant')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Restaurant updated successfully')
        ]
    )]
    public function update(UpdateRestaurantRequest $request, int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->update($id, $request->validated());

        return $this->successResponse(
            new RestaurantResource($restaurant),
            'Restaurant updated successfully'
        );
    }

    #[OA\Patch(
        path: '/restaurants/{restaurant}/operational-status',
        summary: 'Update restaurant operational status',
        tags: ['Restaurants'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'is_open', type: 'boolean', example: true),
                    new OA\Property(property: 'reason', type: 'string', example: 'Morning prep'),
                    new OA\Property(property: 'changed_by', type: 'string', example: 'Admin')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Restaurant operational status updated successfully')
        ]
    )]
    public function updateOperationalStatus(UpdateRestaurantOperationalStatusRequest $request, int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->updateOperationalStatus(
            $id,
            $request->validated('is_open'),
            $request->validated('reason'),
            $request->validated('changed_by')
        );

        return $this->successResponse(
            new RestaurantResource($restaurant),
            'Restaurant operational status updated successfully'
        );
    }
}
