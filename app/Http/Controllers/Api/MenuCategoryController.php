<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCategoryRequest;
use App\Http\Requests\UpdateMenuCategoryRequest;
use App\Http\Resources\MenuCategoryResource;
use App\Services\MenuCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MenuCategoryController extends Controller
{
    use ApiResponseHelper;

    public function __construct(protected MenuCategoryService $categoryService) {}

    #[OA\Get(
        path: '/restaurants/{restaurant}/categories',
        summary: 'Get all categories for a restaurant',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function index(Request $request, int $restaurantId): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $categories = $this->categoryService->listByRestaurant($restaurantId, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Menu categories retrieved successfully',
            'data' => MenuCategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/restaurants/{restaurant}/categories/all',
        summary: 'Get all categories with cursor pagination (for bulk fetch)',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 500)),
            new OA\Parameter(name: 'cursor', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function all(Request $request, int $restaurantId): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 500), 1000);
        $categories = $this->categoryService->bulkListByRestaurant($restaurantId, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Menu categories bulk retrieved successfully',
            'data' => MenuCategoryResource::collection($categories),
            'meta' => [
                'next_cursor' => $categories->nextCursor()?->encode(),
                'prev_cursor' => $categories->previousCursor()?->encode(),
                'per_page' => $categories->perPage(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/restaurants/{restaurant}/categories',
        summary: 'Create a category for a restaurant',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'restaurant', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreMenuCategoryRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Menu category created successfully'),
        ]
    )]
    public function store(StoreMenuCategoryRequest $request, int $restaurantId): JsonResponse
    {
        $category = $this->categoryService->create($restaurantId, $request->validated());

        return $this->successResponse(
            new MenuCategoryResource($category),
            'Menu category created successfully',
            201
        );
    }

    #[OA\Get(
        path: '/categories/{category}',
        summary: 'Get category details',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findOrFail($id);

        return $this->successResponse(
            new MenuCategoryResource($category),
            'Menu category retrieved successfully'
        );
    }

    #[OA\Put(
        path: '/categories/{category}',
        summary: 'Update a category',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateMenuCategoryRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function update(UpdateMenuCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->update($id, $request->validated());

        return $this->successResponse(
            new MenuCategoryResource($category),
            'Menu category updated successfully'
        );
    }

    #[OA\Delete(
        path: '/categories/{category}',
        summary: 'Delete a category',
        tags: ['Menu Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->delete($id);

        return $this->successResponse(null, 'Menu category deleted successfully');
    }
}
