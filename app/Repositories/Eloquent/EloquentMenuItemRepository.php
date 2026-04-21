<?php

namespace App\Repositories\Eloquent;

use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    public function __construct(
        protected MenuItem $model,
    ) {}

    public function getByRestaurant(int $restaurantId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('restaurant_id', $restaurantId);

        if (isset($filters['menu_category_id'])) {
            $query->where('menu_category_id', $filters['menu_category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->with('menuCategory')->latest()->paginate($perPage);
    }

    public function findById(int $id): ?MenuItem
    {
        return $this->model->with('menuCategory')->find($id);
    }

    public function create(array $data): MenuItem
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): MenuItem
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);

        return $item->fresh(['menuCategory']);
    }

    public function updateAvailability(int $id, bool $isAvailable): MenuItem
    {
        $item = $this->model->findOrFail($id);
        $item->update(['is_available' => $isAvailable]);

        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        $item = $this->model->findOrFail($id);

        return (bool) $item->delete(); // soft delete
    }

    /**
     * Batch-fetch orderable items — single query, no N+1.
     * Uses the composite index (restaurant_id, is_active, is_available).
     */
    public function findOrderableItemsByRestaurantAndIds(int $restaurantId, array $itemIds): Collection
    {
        return $this->model
            ->where('restaurant_id', $restaurantId)
            ->whereIn('id', $itemIds)
            ->get();
    }
}
