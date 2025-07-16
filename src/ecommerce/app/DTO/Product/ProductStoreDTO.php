<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for storing a new product.
 */
readonly class ProductStoreDTO
{
    /**
     * @param string $name
     * @param string $article
     * @param string $description
     * @param string $releaseDate
     * @param float $price
     * @param mixed $image
     * @param int $manufacturerId
     * @param int $categoryId
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public string $name,
        public string $article,
        public string $description,
        public string $releaseDate,
        public float $price,
        public mixed $image,
        public int $manufacturerId,
        public int $categoryId,
        public ?array $maintenances,
    ) {
    }
}
