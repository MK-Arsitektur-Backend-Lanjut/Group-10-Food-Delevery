<?php

use App\Models\Restaurant;
use App\Models\RestaurantStatusLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a restaurant', function () {
    $data = [
        'name' => 'Test Restaurant',
        'address' => 'Jl. Test No. 1',
        'is_open' => true,
    ];

    $response = $this->postJson('/api/v1/restaurants', $data);

    $response->assertStatus(201)
             ->assertJsonPath('data.name', 'Test Restaurant')
             ->assertJsonPath('data.is_open', true);

    $this->assertDatabaseHas('restaurants', ['name' => 'Test Restaurant']);
});

it('can update operational status and logs it', function () {
    $restaurant = Restaurant::factory()->closed()->create();

    $response = $this->patchJson("/api/v1/restaurants/{$restaurant->id}/operational-status", [
        'is_open' => true,
        'reason' => 'Morning open',
        'changed_by' => 'Admin'
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.is_open', true);

    $this->assertDatabaseHas('restaurants', [
        'id' => $restaurant->id,
        'is_open' => true,
    ]);

    $this->assertDatabaseHas('restaurant_status_logs', [
        'restaurant_id' => $restaurant->id,
        'new_is_open लड़ाई' => true, // Wait, typo check
        'new_is_open' => true,
        'reason' => 'Morning open'
    ]);
});
