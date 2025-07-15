<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for storing a new product.
 */
readonly class ProductStoreDTO
{
    public function __construct(
        public string $name,
        public string $article,
        public string $description,
        public string $release_date,
        public float $price,
        public mixed $image,
        public int $manufacturer_id,
        public int $category_id,
        public array $maintenances = [],
    ) {}
}
