<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string|null $description
     * @param string|null $release_date
     * @param float $price
     * @param string|null $image_path
     * @param int $manufacturer_id
     * @param string|null $manufacturer_name
     * @param int $category_id
     * @param string|null $category_name
     * @param string $created_at
     * @param string $updated_at
     */
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