<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns cursor paginated orders via /api/orders/all', function () {
    $user = User::factory()->create();

    Order::factory()
        ->count(50)
        ->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/orders/all?per_page=20');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'restaurant_id',
                    'driver_id',
                    'status',
                    'total_price',
                    'created_at',
                    'user' => ['id', 'name'],
                ],
            ],
            'meta' => [
                'path',
                'per_page',
                'next_cursor',
                'prev_cursor',
            ],
        ]);

    expect($response->json('data'))->toHaveCount(20);
    expect($response->json('meta.next_cursor'))->not->toBeNull();
});

it('can iterate all records using cursor pagination', function () {
    $user = User::factory()->create();

    Order::factory()
        ->count(100)
        ->create(['user_id' => $user->id]);

    $collectedIds = [];
    $cursor = null;

    do {
        $url = '/api/orders/all?per_page=30';
        if ($cursor) {
            $url .= '&cursor='.$cursor;
        }

        $response = $this->getJson($url);
        $response->assertStatus(200);

        $data = $response->json('data');
        foreach ($data as $order) {
            $collectedIds[] = $order['id'];
        }

        $cursor = $response->json('meta.next_cursor');
    } while ($cursor !== null);

    expect($collectedIds)->toHaveCount(100);
    expect(array_unique($collectedIds))->toHaveCount(100);
});

it('caps per_page to 10000 for cursor pagination', function () {
    Order::factory()->count(10)->create();

    $response = $this->getJson('/api/orders/all?per_page=15000');
    
    $response->assertStatus(200);
    // Jika ada 10 data, harusnya balik 10
    expect(count($response->json('data')))->toBe(10);
});

it('caps per_page to 100 for standard pagination', function () {
    $user = User::factory()->create();

    Order::factory()
        ->count(5)
        ->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/orders?per_page=500');

    $response->assertStatus(200);
    expect($response->json('meta.per_page'))->toBe(100);
});

it('returns OrderResource structure from standard index endpoint', function () {
    $user = User::factory()->create();

    Order::factory()
        ->count(3)
        ->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'restaurant_id',
                    'status',
                    'total_price',
                    'created_at',
                    'user' => ['id', 'name'],
                ],
            ],
        ]);
});
