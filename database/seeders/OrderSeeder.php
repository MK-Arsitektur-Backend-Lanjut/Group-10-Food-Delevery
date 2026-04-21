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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
  public function run()
  {
    for ($i = 0; $i < 10000; $i++) {
        Order::create([
            'user_id' => rand(1, 100),
            'restaurant_id' => rand(1, 10),
            'status' => 'DIPESAN',
            'total_price' => rand(10000, 50000)
        ]);
      }
    } 
}
