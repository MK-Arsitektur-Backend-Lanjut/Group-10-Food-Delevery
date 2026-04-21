<?php

namespace Database\Seeders;

use App\Models\DeliveryHistory;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Driver::count() === 0) {
            Driver::factory(50)->create();
        }

        Order::factory(10000)->create()->each(function (Order $order): void {
            $driver = Driver::inRandomOrder()->first();

            DeliveryHistory::create([
                'driver_id' => $driver->id,
                'order_id' => $order->id,
                'delivered_at' => now(),
            ]);
        });
    }
}
