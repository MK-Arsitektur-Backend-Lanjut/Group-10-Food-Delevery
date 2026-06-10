<?php

namespace App\Repositories\Contracts;

use App\Models\MenuCategory;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MenuCategoryRepositoryInterface
{
    public function getByRestaurant(int $restaurantId, int $perPage = 15): LengthAwarePaginator;

    public function cursorPaginateByRestaurant(int $restaurantId, int $perPage = 500): CursorPaginator;

    public function findById(int $id): ?MenuCategory;

    public function create(array $data): MenuCategory;

    public function update(int $id, array $data): MenuCategory;

    public function delete(int $id): bool;
}
