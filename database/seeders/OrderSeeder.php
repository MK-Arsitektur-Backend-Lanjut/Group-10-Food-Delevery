<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderStatusLog;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 10000; $i++) {

            $order = Order::create([
                'user_id' => rand(1, 100),
                'restaurant_id' => rand(1, 10),
                'status' => 'SELESAI',
                'total_price' => rand(10000, 50000),
            ]);

            // histori status
            $statuses = ['DIPESAN', 'DIMASAK', 'DIANTAR', 'SELESAI'];

            foreach ($statuses as $status) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}