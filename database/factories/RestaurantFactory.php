<?php

namespace Database\Factories;

use App\Enums\RestaurantStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Restaurant',
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'status' => RestaurantStatus::ACTIVE,
            'is_open' => true,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RestaurantStatus::INACTIVE,
        ]);
    }
}
