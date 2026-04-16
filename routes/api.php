<?php

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
    });
});
