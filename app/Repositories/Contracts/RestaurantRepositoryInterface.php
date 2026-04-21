<?php

namespace App\Repositories\Contracts;

use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RestaurantRepositoryInterface
{
    public function findById(int $id): ?Restaurant;

    public function findActiveById(int $id): ?Restaurant;

    public function create(array $data): Restaurant;

    public function update(int $id, array $data): Restaurant;

    public function updateOperationalStatus(int $id, bool $isOpen): Restaurant;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
