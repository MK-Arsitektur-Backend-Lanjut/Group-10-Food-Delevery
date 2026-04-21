<?php

namespace App\Repositories\Eloquent;

use App\Models\RestaurantStatusLog;
use App\Repositories\Contracts\RestaurantStatusLogRepositoryInterface;

class EloquentRestaurantStatusLogRepository implements RestaurantStatusLogRepositoryInterface
{
    public function __construct(
        protected RestaurantStatusLog $model,
    ) {}

    public function create(array $data): RestaurantStatusLog
    {
        return $this->model->create($data);
    }
}
