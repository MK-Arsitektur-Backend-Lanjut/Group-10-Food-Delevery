<?php

namespace App\Services;

use App\Models\MenuCategory;
use App\Repositories\Contracts\MenuCategoryRepositoryInterface;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuCategoryService
{
    public function __construct(
        protected MenuCategoryRepositoryInterface $categoryRepo,
        protected RestaurantRepositoryInterface $restaurantRepo,
    ) {}

    public function listByRestaurant(int $restaurantId): Collection
    {
        $this->ensureRestaurantExists($restaurantId);

        return $this->categoryRepo->getByRestaurant($restaurantId);
    }

    public function findOrFail(int $id): MenuCategory
    {
        $category = $this->categoryRepo->findById($id);

        if (! $category) {
            abort(404, 'Menu category not found.');
        }

        return $category;
    }

    public function create(int $restaurantId, array $data): MenuCategory
    {
        $this->ensureRestaurantExists($restaurantId);

        $data['restaurant_id'] = $restaurantId;

        return $this->categoryRepo->create($data);
    }

    public function update(int $id, array $data): MenuCategory
    {
        $this->findOrFail($id);

        return $this->categoryRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $this->findOrFail($id);

        return $this->categoryRepo->delete($id);
    }

    protected function ensureRestaurantExists(int $restaurantId): void
    {
        if (! $this->restaurantRepo->findById($restaurantId)) {
            abort(404, 'Restaurant not found.');
        }
    }
}
