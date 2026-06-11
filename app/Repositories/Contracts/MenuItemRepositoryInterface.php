<?php

namespace App\Repositories\Contracts;

use App\Models\MenuItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MenuItemRepositoryInterface
{
    public function getByRestaurant(int $restaurantId, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?MenuItem;

    public function create(array $data): MenuItem;

    public function update(int $id, array $data): MenuItem;

    public function updateAvailability(int $id, bool $isAvailable): MenuItem;

    public function delete(int $id): bool;

    /**
     * Batch-fetch orderable items by restaurant and IDs.
     * Only returns items that are is_active=true AND is_available=true.
     *
     * @return Collection<int, MenuItem>
     */
    public function findOrderableItemsByRestaurantAndIds(int $restaurantId, array $itemIds): Collection;
}
