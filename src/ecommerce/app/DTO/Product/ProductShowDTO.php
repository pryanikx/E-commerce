<?php

namespace App\DTO\Product;

use App\Models\Product;

readonly class ProductShowDTO
{
    public string $name;
    public string $article;
    public string $description;
    public string $release_date;
    public string $category_name;
    public string $manufacturer_name;
    public float $price;
    public string $image_url;
    public array $maintenances;


    public function __construct(Product $product) {
        $this->name = $product->name;
        $this->article = $product->article;
        $this->description = $product->description;
        $this->release_date = $product->release_date;
        $this->category_name = $product->category->name;
        $this->manufacturer_name = $product->manufacturer->name;
        $this->price = $product->price;
        $this->image_url = asset($product->image_path);
        $this->maintenances = $product->maintenances->map(fn ($maintenance) => [
            'name' => $maintenance->name,
            'price' => $maintenance->pivot->price
            ]);
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'article' => $this->article,
            'description' => $this->description,
            'release_date' => $this->release_date,
            'category_name' => $this->category_name,
            'manufacturer_name' => $this->manufacturer_name,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'maintenances' => $this->maintenances,
        ];
    }
}
