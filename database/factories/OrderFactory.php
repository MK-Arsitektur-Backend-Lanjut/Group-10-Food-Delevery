<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'restaurant_id' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['DIPESAN', 'DIMASAK', 'DIANTAR', 'SELESAI']),
            'total_price' => $this->faker->randomFloat(2, 15000, 150000),
            'driver_id' => null,
        ];
    }
}
