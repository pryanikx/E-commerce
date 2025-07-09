<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public ?string $description,
        public ?string $release_date,
        public float $price,
        public ?string $image_path,
        public int $manufacturer_id,
        public ?string $manufacturer_name,
        public int $category_id,
        public ?string $category_name,
        public string $created_at,
        public string $updated_at,
    ) {}
} 