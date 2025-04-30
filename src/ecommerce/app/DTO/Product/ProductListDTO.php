<?php

namespace App\DTO\Product;

use App\Models\Product;

readonly class ProductListDTO
{
    public array $data;

    public function __construct(Product $product) {
        $this->data = [
            'name' => $product->name,
            'article' => $product->article,
            'manufacturer_name' => $product->manufacturer->name,
            'price' => $product->price,
            'image_url' => asset($product->image_path),
        ];
    }

    public function toArray(): array {
        return $this->data;
    }
}
