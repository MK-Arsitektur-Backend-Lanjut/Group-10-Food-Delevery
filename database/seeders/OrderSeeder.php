<?php

namespace Database\Seeders;

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