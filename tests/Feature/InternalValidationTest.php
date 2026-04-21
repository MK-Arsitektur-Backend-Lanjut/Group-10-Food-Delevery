<?php

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validates order items successfully', function () {
    $restaurant = Restaurant::factory()->create(['is_open' => true, 'status' => 'active']);
    
    $item1 = MenuItem::factory()->create(['restaurant_id' => $restaurant->id, 'price' => 15000, 'is_available' => true]);
    $item2 = MenuItem::factory()->create(['restaurant_id' => $restaurant->id, 'price' => 20000, 'is_available' => true]);

    $payload = [
        'restaurant_id' => $restaurant->id,
        'items' => [
            ['menu_item_id' => $item1->id, 'qty' => 2],
            ['menu_item_id' => $item2->id, 'qty' => 1],
            // test duplicate resolution
            ['menu_item_id' => $item1->id, 'qty' => 1], 
        ]
    ];

    $response = $this->postJson('/api/v1/internal/order-items/validate', $payload);

    $response->assertStatus(200)
             ->assertJsonPath('valid', true)
             ->assertJsonPath('errors', [])
             ->assertJsonCount(2, 'items');

    // Verify duplicate was merged (2 + 1 = 3 qty)
    $items = collect($response->json('items'));
    $mergedItem = $items->firstWhere('menu_item_id', $item1->id);
    expect($mergedItem['qty'])->toBe(3);
});

it('fails validation when restaurant is closed', function () {
    $restaurant = Restaurant::factory()->create(['is_open' => false, 'status' => 'active']);
    $item = MenuItem::factory()->create(['restaurant_id' => $restaurant->id]);

    $payload = [
        'restaurant_id' => $restaurant->id,
        'items' => [
            ['menu_item_id' => $item->id, 'qty' => 1],
        ]
    ];

    $response = $this->postJson('/api/v1/internal/order-items/validate', $payload);

    $response->assertStatus(422)
             ->assertJsonPath('valid', false)
             ->assertJsonPath('errors.0.code', 'RESTAURANT_CLOSED');
});

it('fails validation when an item is unavailable', function () {
    $restaurant = Restaurant::factory()->create(['is_open' => true, 'status' => 'active']);
    $item = MenuItem::factory()->create([
        'restaurant_id' => $restaurant->id, 
        'is_available' => false
    ]);

    $payload = [
        'restaurant_id' => $restaurant->id,
        'items' => [
            ['menu_item_id' => $item->id, 'qty' => 1],
        ]
    ];

    $response = $this->postJson('/api/v1/internal/order-items/validate', $payload);

    $response->assertStatus(422)
             ->assertJsonPath('valid', false)
             ->assertJsonPath('errors.0.code', 'ITEM_NOT_AVAILABLE');
});
