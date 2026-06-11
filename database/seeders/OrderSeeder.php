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
        if (\App\Models\Driver::count() === 0) {
            \App\Models\Driver::factory(50)->create();
        }
        if (\App\Models\User::count() === 0) {
            \App\Models\User::factory(100)->create();
        }
        if (\App\Models\Restaurant::count() === 0) {
            \App\Models\Restaurant::factory(20)->create();
        }

        $userIds = \App\Models\User::pluck('id')->toArray();
        $restaurantIds = \App\Models\Restaurant::pluck('id')->toArray();
        $driverIds = \App\Models\Driver::pluck('id')->toArray();
        
        $statuses = ['DIPESAN', 'DIMASAK', 'DIANTAR', 'SELESAI'];

        $totalOrders = 10000;
        $chunkSize = 1000;
        $now = now();

        $this->command->info("Menyisipkan {$totalOrders} pesanan dalam batch {$chunkSize}...");

        for ($i = 0; $i < $totalOrders; $i += $chunkSize) {
            $orders = [];
            
            for ($j = 0; $j < $chunkSize; $j++) {
                $orders[] = [
                    'user_id' => $userIds[array_rand($userIds)],
                    'restaurant_id' => $restaurantIds[array_rand($restaurantIds)],
                    'driver_id' => null,
                    'status' => $statuses[array_rand($statuses)],
                    'total_price' => rand(15000, 150000),
                    'created_at' => clone $now,
                    'updated_at' => clone $now,
                ];
            }

            Order::insert($orders);
        }

        $this->command->info("Menyisipkan riwayat pengiriman...");

        // Ambil ID dari 10.000 order terakhir
        Order::latest('id')->take($totalOrders)->chunkById(1000, function ($orders) use ($driverIds, $now) {
            $deliveryHistories = [];
            
            foreach ($orders as $order) {
                $deliveryHistories[] = [
                    'driver_id' => $driverIds[array_rand($driverIds)],
                    'order_id' => $order->id,
                    'delivered_at' => clone $now,
                    'created_at' => clone $now,
                    'updated_at' => clone $now,
                ];
            }
            
            DeliveryHistory::insert($deliveryHistories);
        });

        $this->command->info('Seeding 10.000 pesanan selesai!');
    }
}
