<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductUpdateDTO
{
    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $article
     * @param string|null $description
     * @param string|null $releaseDate
     * @param float|array<string, float> $price ,
     * @param string|null $imagePath
     * @param int|null $manufacturerId
     * @param int|null $categoryId
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $article,
        public ?string $description,
        public ?string $releaseDate,
        public float|array|null $price,
        public ?string $imagePath,
        public ?int $manufacturerId,
        public ?int $categoryId,
        public ?array $maintenances,
    ) {
    }
}
