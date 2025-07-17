<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Manufacturer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = 'Device ' . rand(1, 1000),
            'article' => Str::slug($name),
            'description' => fake()->text(100),
            'release_date' => fake()->date(),
            'price' => fake()->randomFloat(2, 1, 999999),
            'image_path' => fake()->filePath(),
            'category_id' => optional(Category::inRandomOrder()->first())->id ?? 1,
            'manufacturer_id' => optional(Manufacturer::inRandomOrder()->first())->id ?? 1,
        ];
    }
}
