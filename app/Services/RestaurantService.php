<?php

namespace App\Services;

use App\Repositories\Contracts\RestaurantRepositoryInterface;
use App\Repositories\Contracts\RestaurantStatusLogRepositoryInterface;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RestaurantService
{
    public function __construct(
        protected RestaurantRepositoryInterface $restaurantRepo,
        protected RestaurantStatusLogRepositoryInterface $statusLogRepo,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->restaurantRepo->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Restaurant
    {
        $restaurant = $this->restaurantRepo->findById($id);

        if (! $restaurant) {
            abort(404, 'Restaurant not found.');
        }

        return $restaurant;
    }

    public function create(array $data): Restaurant
    {
        return $this->restaurantRepo->create($data);
    }

    public function update(int $id, array $data): Restaurant
    {
        $this->findOrFail($id);

        return $this->restaurantRepo->update($id, $data);
    }

    /**
     * Update operational status (is_open) idempotently.
     * Logs the change only when the value actually changes.
     */
    public function updateOperationalStatus(int $id, bool $isOpen, ?string $reason = null, ?string $changedBy = null): Restaurant
    {
        $restaurant = $this->findOrFail($id);

        // Idempotent: if status is already the same, return early without logging
        if ($restaurant->is_open === $isOpen) {
            return $restaurant;
        }

        return DB::transaction(function () use ($restaurant, $isOpen, $reason, $changedBy) {
            $previousIsOpen = $restaurant->is_open;

            $updated = $this->restaurantRepo->updateOperationalStatus($restaurant->id, $isOpen);

            $this->statusLogRepo->create([
                'restaurant_id' => $restaurant->id,
                'previous_is_open' => $previousIsOpen,
                'new_is_open' => $isOpen,
                'reason' => $reason,
                'changed_by' => $changedBy,
            ]);

            return $updated;
        });
    }
}
