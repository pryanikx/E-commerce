<?php

namespace App\DTO\Product;

use App\Models\Product;

readonly class ProductShowDTO
{
    public array $data;

    public function __construct(Product $product) {
        $this->data = [
            'name' => $product->name,
            'article' => $product->article,
            'description' => $product->description,
            'release_date' => $product->release_date,
            'category_name' => $product->category->name,
            'manufacturer_name' => $product->manufacturer->name,
            'price' => $product->price,
            'image_url' => asset($product->image_path),
            'services' => $product->services->map(fn ($service) => [
                'name' => $service->name,
                'price' => $service->pivot->price
            ])
        ];
    }

    public function toArray(): array {
        return $this->data;
    }
}
