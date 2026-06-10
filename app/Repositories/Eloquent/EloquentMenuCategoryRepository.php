<?php

namespace App\Repositories\Eloquent;

use App\Models\MenuCategory;
use App\Repositories\Contracts\MenuCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentMenuCategoryRepository implements MenuCategoryRepositoryInterface
{
    public function __construct(
        protected MenuCategory $model,
    ) {}

    public function getByRestaurant(int $restaurantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('restaurant_id', $restaurantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function cursorPaginateByRestaurant(int $restaurantId, int $perPage = 500): CursorPaginator
    {
        return $this->model
            ->where('restaurant_id', $restaurantId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->cursorPaginate($perPage);
    }

    public function findById(int $id): ?MenuCategory
    {
        return $this->model->find($id);
    }

    public function create(array $data): MenuCategory
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): MenuCategory
    {
        $category = $this->model->findOrFail($id);
        $category->update($data);

        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        $category = $this->model->findOrFail($id);

        return (bool) $category->delete(); // soft delete
    }
}
