<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Modul1Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Seed 30 realistic restaurants
            Restaurant::factory(30)->create()->each(function (Restaurant $restaurant) {
                
                // Each restaurant has 3-5 categories
                $categories = MenuCategory::factory(rand(3, 5))->create([
                    'restaurant_id' => $restaurant->id,
                ]);

                // Each category has 5-10 menu items
                foreach ($categories as $category) {
                    MenuItem::factory(rand(5, 10))->create([
                        'restaurant_id' => $restaurant->id,
                        'menu_category_id' => $category->id,
                    ]);
                }
            });
        });
    }
}
