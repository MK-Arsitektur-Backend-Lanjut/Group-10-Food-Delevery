<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuAvailabilityRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Services\MenuItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MenuItemController extends Controller
{
    use ApiResponseHelper;

    public function __construct(protected MenuItemService $menuItemService)
    {
    }

    #[OA\Get(
        path: '/restaurants/{restaurant}/menus',
        summary: 'Get all menu items for a restaurant',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function index(Request $request, int $restaurantId): JsonResponse
    {
        $items = $this->menuItemService->listByRestaurant($restaurantId, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Menu items retrieved successfully',
            'data' => MenuItemResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ]);
    }

    #[OA\Post(
        path: '/restaurants/{restaurant}/menus',
        summary: 'Create a menu item for a restaurant',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreMenuItemRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Menu item created successfully')
        ]
    )]
    public function store(StoreMenuItemRequest $request, int $restaurantId): JsonResponse
    {
        $item = $this->menuItemService->create($restaurantId, $request->validated());

        return $this->successResponse(
            new MenuItemResource($item),
            'Menu item created successfully',
            201
        );
    }

    #[OA\Get(
        path: '/menus/{menu}',
        summary: 'Get menu item details',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'menu', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $item = $this->menuItemService->findOrFail($id);

        return $this->successResponse(
            new MenuItemResource($item),
            'Menu item retrieved successfully'
        );
    }

    #[OA\Put(
        path: '/menus/{menu}',
        summary: 'Update a menu item',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'menu', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateMenuItemRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function update(UpdateMenuItemRequest $request, int $id): JsonResponse
    {
        $item = $this->menuItemService->update($id, $request->validated());

        return $this->successResponse(
            new MenuItemResource($item),
            'Menu item updated successfully'
        );
    }

    #[OA\Patch(
        path: '/menus/{menu}/availability',
        summary: 'Update menu item availability',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'menu', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateMenuAvailabilityRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Menu item availability updated successfully')
        ]
    )]
    public function updateAvailability(UpdateMenuAvailabilityRequest $request, int $id): JsonResponse
    {
        $item = $this->menuItemService->updateAvailability($id, $request->validated('is_available'));

        return $this->successResponse(
            new MenuItemResource($item),
            'Menu item availability updated successfully'
        );
    }

    #[OA\Delete(
        path: '/menus/{menu}',
        summary: 'Delete a menu item',
        tags: ['Menu Items'],
        parameters: [
            new OA\Parameter(name: 'menu', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->menuItemService->delete($id);

        return $this->successResponse(null, 'Menu item deleted successfully');
    }
}
