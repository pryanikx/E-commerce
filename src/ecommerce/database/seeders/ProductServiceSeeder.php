<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Service;

class ProductServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = Product::all();
        $services = Service::all();
    
        foreach ($products as $product) {
            $randomServices = $services->random(rand(1, 3));
    
            foreach ($randomServices as $service) {
                if (!DB::table('products_services')
                    ->where('product_id', $product->id)
                    ->where('service_id', $service->id)
                    ->exists()) {
                    
                    DB::table('products_services')->insert([
                        'product_id' => $product->id,
                        'service_id' => $service->id,
                        'price' => fake()->randomFloat(2, 10, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
    
}
