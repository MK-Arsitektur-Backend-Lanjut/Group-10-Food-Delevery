<?php

namespace App\Repositories\Eloquent;

use App\Models\Restaurant;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentRestaurantRepository implements RestaurantRepositoryInterface
{
    public function __construct(
        protected Restaurant $model,
    ) {}

    public function findById(int $id): ?Restaurant
    {
        return $this->model->find($id);
    }

    public function findActiveById(int $id): ?Restaurant
    {
        return $this->model
            ->where('id', $id)
            ->where('status', 'active')
            ->first();
    }

    public function create(array $data): Restaurant
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Restaurant
    {
        $restaurant = $this->model->findOrFail($id);
        $restaurant->update($data);

        return $restaurant->fresh();
    }

    public function updateOperationalStatus(int $id, bool $isOpen): Restaurant
    {
        $restaurant = $this->model->findOrFail($id);
        $restaurant->update(['is_open' => $isOpen]);

        return $restaurant->fresh();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_open'])) {
            $query->where('is_open', $filters['is_open']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('address', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }
}
