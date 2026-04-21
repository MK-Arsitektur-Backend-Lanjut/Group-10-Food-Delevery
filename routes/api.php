<?php
      
use App\Http\Controllers\Api\InternalIntegrationController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Driver Routes
Route::prefix('drivers')->group(function () {
    Route::post('login', [DriverController::class, 'login']);
    Route::post('register', [DriverController::class, 'register']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [DriverController::class, 'index']);
        Route::post('/', [DriverController::class, 'store']);
        Route::get('available', [DriverController::class, 'available']); 
        Route::get('{id}', [DriverController::class, 'show']);
        Route::put('{id}', [DriverController::class, 'update']);
        Route::delete('{id}', [DriverController::class, 'destroy']);// Pencarian driver tersedia
        Route::get('{id}/history', [DriverController::class, 'history']); // Riwayat pengantaran
      
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
