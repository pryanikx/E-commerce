<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductStoreDTO
{
    /**
     * @param string $name
     * @param string $article
     * @param string|null $description
     * @param string $releaseDate
     * @param float|array<string, float> $price,
     * @param string|null $imagePath
     * @param int $manufacturerId
     * @param int $categoryId
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public string $name,
        public string $article,
        public ?string $description,
        public string $releaseDate,
        public float|array $price,
        public ?string $imagePath,
        public int $manufacturerId,
        public int $categoryId,
        public ?array $maintenances,
    ) {
    }
}
