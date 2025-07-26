<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string $description
     * @param string $releaseDate
     * @param float|array<string, float> $price,
     * @param string|null $imagePath
     * @param int $manufacturerId
     * @param string $manufacturerName
     * @param int $categoryId
     * @param string $categoryName
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $description,
        public string $releaseDate,
        public float|array $price,
        public ?string $imagePath,
        public int $manufacturerId,
        public string $manufacturerName,
        public int $categoryId,
        public string $categoryName,
        public ?array $maintenances,
    ) {
    }
}
