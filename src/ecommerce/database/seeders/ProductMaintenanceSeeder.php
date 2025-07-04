<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Maintenance;

class ProductMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = Product::all();
        $maintenances = Maintenance::all();

        foreach ($products as $product) {
            $randomMaintenances = $maintenances->random(rand(1, 3));

            foreach ($randomMaintenances as $maintenance) {
                if (!DB::table('products_maintenances')
                    ->where('product_id', $product->id)
                    ->where('maintenance_id', $maintenance->id)
                    ->exists()) {

                    DB::table('products_maintenances')->insert([
                        'product_id' => $product->id,
                        'maintenance_id' => $maintenance->id,
                        'price' => fake()->randomFloat(2, 10, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
