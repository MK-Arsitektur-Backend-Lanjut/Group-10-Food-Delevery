<?php

use App\Http\Controllers\Api\InternalIntegrationController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

Route::prefix('v1')->group(function () {
    // Restaurants
    Route::apiResource('restaurants', RestaurantController::class);
    Route::patch('restaurants/{restaurant}/operational-status', [RestaurantController::class, 'updateOperationalStatus']);

    // Menu Categories (nested under restaurant for list/create, isolated for direct manip)
    Route::get('restaurants/{restaurant}/categories', [MenuCategoryController::class, 'index']);
    Route::post('restaurants/{restaurant}/categories', [MenuCategoryController::class, 'store']);
    Route::get('categories/{category}', [MenuCategoryController::class, 'show']);
    Route::put('categories/{category}', [MenuCategoryController::class, 'update']);
    Route::delete('categories/{category}', [MenuCategoryController::class, 'destroy']);

    // Menu Items
    Route::get('restaurants/{restaurant}/menus', [MenuItemController::class, 'index']);
    Route::post('restaurants/{restaurant}/menus', [MenuItemController::class, 'store']);
    Route::get('menus/{menu}', [MenuItemController::class, 'show']);
    Route::put('menus/{menu}', [MenuItemController::class, 'update']);
    Route::patch('menus/{menu}/availability', [MenuItemController::class, 'updateAvailability']);
    Route::delete('menus/{menu}', [MenuItemController::class, 'destroy']);

    // Internal Integration Layer
    Route::prefix('internal')->group(function () {
        Route::post('order-items/validate', [InternalIntegrationController::class, 'validateOrderItems']);
    });
});
