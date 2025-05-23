<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fridges', 'alias' => 'fridges'],
            ['name' => 'Phones', 'alias' => 'phones'],
            ['name' => 'Laptops', 'alias' => 'laptops'],
            ['name' => 'TV', 'alias' => 'tv'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
