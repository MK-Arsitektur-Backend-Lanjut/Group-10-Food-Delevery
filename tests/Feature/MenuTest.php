<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can fetch menu categories for a restaurant', function () {
    $restaurant = Restaurant::factory()->create();
    MenuCategory::factory()->count(3)->create(['restaurant_id' => $restaurant->id]);

    $response = $this->getJson("/api/v1/restaurants/{$restaurant->id}/categories");

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
});

it('can create a menu item with valid category', function () {
    $restaurant = Restaurant::factory()->create();
    $category = MenuCategory::factory()->create(['restaurant_id' => $restaurant->id]);

    $data = [
        'menu_category_id' => $category->id,
        'name' => 'Nasi Bakar',
        'price' => 30000,
        'is_available' => true,
    ];

    $response = $this->postJson("/api/v1/restaurants/{$restaurant->id}/menus", $data);

    $response->assertStatus(201)
             ->assertJsonPath('data.name', 'Nasi Bakar');
});

it('prevents creating menu item with wrong restaurant category', function () {
    $restaurant1 = Restaurant::factory()->create();
    $restaurant2 = Restaurant::factory()->create();
    
    // Category belongs to restaurant 2
    $category = MenuCategory::factory()->create(['restaurant_id' => $restaurant2->id]);

    $data = [
        'menu_category_id' => $category->id,
        'name' => 'Nasi Bakar',
        'price' => 30000,
    ];

    // Trying to add to restaurant 1 but using category from rest 2
    $response = $this->postJson("/api/v1/restaurants/{$restaurant1->id}/menus", $data);

    $response->assertStatus(422);
});

it('can update menu item availability', function () {
    $item = MenuItem::factory()->create(['is_available' => false]);

    $response = $this->patchJson("/api/v1/menus/{$item->id}/availability", [
        'is_available' => true
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.is_available', true);
});
