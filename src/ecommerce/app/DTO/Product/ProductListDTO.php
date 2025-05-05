<?php

namespace App\DTO\Product;

use App\Models\Product;

readonly class ProductListDTO
{
    public string $name;
    public string $article;
    public string $manufacturer_name;
    public float $price;
    public string $image_url;
    public array $data;

    public function __construct(Product $product) {
        $this->name = $product->name;
        $this->article = $product->article;
        $this->manufacturer_name = $product->manufacturer->name;
        $this->price = $product->price;
        $this->image_url = asset($product->image_path);
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'article' => $this->article,
            'manufacturer_name' => $this->manufacturer_name,
            'price' => $this->price,
            'image_url' => $this->image_url,
        ];
    }
}
