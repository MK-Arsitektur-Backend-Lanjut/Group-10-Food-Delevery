<?php

namespace App\Services;

use App\Enums\RestaurantStatus;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\RestaurantRepositoryInterface;

class MenuValidationService
{
    public function __construct(
        protected RestaurantRepositoryInterface $restaurantRepo,
        protected MenuItemRepositoryInterface $menuItemRepo,
    ) {}

    /**
     * Validate order items for a given restaurant.
     *
     * Steps:
     * 1. Normalize duplicate menu_item_ids (merge qty)
     * 2. Validate restaurant exists, is active, is open
     * 3. Batch-fetch items (single query)
     * 4. Check each item: exists, belongs to restaurant, is_active, is_available
     * 5. Return canonical result
     */
    public function validate(int $restaurantId, array $items): array
    {
        $errors = [];

        // ── Step 1: Normalize duplicates ──
        $normalized = $this->normalizeItems($items);

        // ── Step 2: Validate restaurant ──
        $restaurant = $this->restaurantRepo->findById($restaurantId);

        if (! $restaurant) {
            return $this->failureResponse(null, [], [
                ['code' => 'RESTAURANT_NOT_FOUND', 'message' => 'Restaurant not found'],
            ]);
        }

        $restaurantData = [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'is_open' => $restaurant->is_open,
            'status' => $restaurant->status->value,
        ];

        if ($restaurant->status === RestaurantStatus::INACTIVE) {
            return $this->failureResponse($restaurantData, [], [
                ['code' => 'RESTAURANT_INACTIVE', 'message' => 'Restaurant is currently inactive'],
            ]);
        }

        if ($restaurant->status === RestaurantStatus::SUSPENDED) {
            return $this->failureResponse($restaurantData, [], [
                ['code' => 'RESTAURANT_SUSPENDED', 'message' => 'Restaurant is currently suspended'],
            ]);
        }

        if (! $restaurant->is_open) {
            return $this->failureResponse($restaurantData, [], [
                ['code' => 'RESTAURANT_CLOSED', 'message' => 'Restaurant is currently closed'],
            ]);
        }

        // ── Step 3: Batch-fetch items (single query, no N+1) ──
        $requestedIds = array_keys($normalized);
        $foundItems = $this->menuItemRepo->findOrderableItemsByRestaurantAndIds($restaurantId, $requestedIds);
        $foundItemsKeyed = $foundItems->keyBy('id');

        // ── Step 4: Validate each item ──
        $validItems = [];

        foreach ($normalized as $menuItemId => $qty) {
            if (! $foundItemsKeyed->has($menuItemId)) {
                // Check if item exists at all (could be wrong restaurant)
                $errors[] = [
                    'code' => 'ITEM_NOT_FOUND',
                    'message' => "Menu item #{$menuItemId} not found in this restaurant",
                ];
                continue;
            }

            $item = $foundItemsKeyed->get($menuItemId);

            if ($item->restaurant_id !== $restaurantId) {
                $errors[] = [
                    'code' => 'ITEM_RESTAURANT_MISMATCH',
                    'message' => "Menu item #{$menuItemId} does not belong to this restaurant",
                ];
                continue;
            }

            if (! $item->is_active) {
                $errors[] = [
                    'code' => 'ITEM_INACTIVE',
                    'message' => "Menu item '{$item->name}' is currently inactive",
                ];
                continue;
            }

            if (! $item->is_available) {
                $errors[] = [
                    'code' => 'ITEM_NOT_AVAILABLE',
                    'message' => "Menu item '{$item->name}' is currently unavailable",
                ];
                continue;
            }

            $validItems[] = [
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'unit_price' => $item->price,
                'qty' => $qty,
            ];
        }

        if (! empty($errors)) {
            return $this->failureResponse($restaurantData, [], $errors);
        }

        return [
            'valid' => true,
            'restaurant' => $restaurantData,
            'items' => $validItems,
            'errors' => [],
        ];
    }

    /**
     * Merge duplicate menu_item_ids by summing qty.
     *
     * @return array<int, int>  [menu_item_id => total_qty]
     */
    protected function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $id = (int) $item['menu_item_id'];
            $qty = (int) $item['qty'];
            $normalized[$id] = ($normalized[$id] ?? 0) + $qty;
        }

        return $normalized;
    }

    protected function failureResponse(?array $restaurant, array $items, array $errors): array
    {
        return [
            'valid' => false,
            'restaurant' => $restaurant,
            'items' => $items,
            'errors' => $errors,
        ];
    }
}
