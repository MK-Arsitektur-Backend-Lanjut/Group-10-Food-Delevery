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
            'status' => $this->faker->randomElement(['pending', 'cooking', 'delivering', 'completed']),
            'total' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
