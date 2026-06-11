<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Repositories\Contracts\MenuCategoryRepositoryInterface;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuItemService
{
    public function __construct(
        protected MenuItemRepositoryInterface $menuItemRepo,
        protected RestaurantRepositoryInterface $restaurantRepo,
        protected MenuCategoryRepositoryInterface $categoryRepo,
    ) {}

    public function listByRestaurant(int $restaurantId, array $filters = []): LengthAwarePaginator
    {
        $this->ensureRestaurantExists($restaurantId);

        return $this->menuItemRepo->getByRestaurant($restaurantId, $filters);
    }

    public function findOrFail(int $id): MenuItem
    {
        $item = $this->menuItemRepo->findById($id);

        if (! $item) {
            abort(404, 'Menu item not found.');
        }

        return $item;
    }

    public function create(int $restaurantId, array $data): MenuItem
    {
        $this->ensureRestaurantExists($restaurantId);

        // Validate category belongs to the same restaurant
        if (! empty($data['menu_category_id'])) {
            $this->ensureCategoryBelongsToRestaurant($data['menu_category_id'], $restaurantId);
        }

        $data['restaurant_id'] = $restaurantId;

        return $this->menuItemRepo->create($data);
    }

    public function update(int $id, array $data): MenuItem
    {
        $item = $this->findOrFail($id);

        // Validate category belongs to the same restaurant
        if (! empty($data['menu_category_id'])) {
            $this->ensureCategoryBelongsToRestaurant($data['menu_category_id'], $item->restaurant_id);
        }

        return $this->menuItemRepo->update($id, $data);
    }

    public function updateAvailability(int $id, bool $isAvailable): MenuItem
    {
        $this->findOrFail($id);

        return $this->menuItemRepo->updateAvailability($id, $isAvailable);
    }

    public function delete(int $id): bool
    {
        $this->findOrFail($id);

        return $this->menuItemRepo->delete($id);
    }

    protected function ensureRestaurantExists(int $restaurantId): void
    {
        if (! $this->restaurantRepo->findById($restaurantId)) {
            abort(404, 'Restaurant not found.');
        }
    }

    protected function ensureCategoryBelongsToRestaurant(int $categoryId, int $restaurantId): void
    {
        $category = $this->categoryRepo->findById($categoryId);

        if (! $category || $category->restaurant_id !== $restaurantId) {
            abort(422, 'The selected category does not belong to this restaurant.');
        }
    }
}
