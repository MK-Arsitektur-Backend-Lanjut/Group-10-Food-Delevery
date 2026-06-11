<?php

namespace App\Repositories\Contracts;

use App\Models\MenuCategory;
use Illuminate\Database\Eloquent\Collection;

interface MenuCategoryRepositoryInterface
{
    public function getByRestaurant(int $restaurantId): Collection;

    public function findById(int $id): ?MenuCategory;

    public function create(array $data): MenuCategory;

    public function update(int $id, array $data): MenuCategory;

    public function delete(int $id): bool;
}
