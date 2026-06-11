<?php

namespace App\Repositories\Contracts;

use App\Models\RestaurantStatusLog;

interface RestaurantStatusLogRepositoryInterface
{
    public function create(array $data): RestaurantStatusLog;
}
