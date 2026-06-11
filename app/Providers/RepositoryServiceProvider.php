<?php

namespace App\Providers;

use App\Repositories\Contracts\MenuCategoryRepositoryInterface;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use App\Repositories\Contracts\RestaurantStatusLogRepositoryInterface;
use App\Repositories\Eloquent\EloquentMenuCategoryRepository;
use App\Repositories\Eloquent\EloquentMenuItemRepository;
use App\Repositories\Eloquent\EloquentRestaurantRepository;
use App\Repositories\Eloquent\EloquentRestaurantStatusLogRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings for the application.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        RestaurantRepositoryInterface::class => EloquentRestaurantRepository::class,
        MenuCategoryRepositoryInterface::class => EloquentMenuCategoryRepository::class,
        MenuItemRepositoryInterface::class => EloquentMenuItemRepository::class,
        RestaurantStatusLogRepositoryInterface::class => EloquentRestaurantStatusLogRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
