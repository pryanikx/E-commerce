<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for updating a product.
 */
readonly class ProductUpdateDTO
{
    /**
     * @param string|null $name
     * @param string|null $article
     * @param string|null $description
     * @param string|null $releaseDate
     * @param float|null $price
     * @param mixed|null $image
     * @param int|null $manufacturerId
     * @param int|null $categoryId
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public ?string $name = null,
        public ?string $article = null,
        public ?string $description = null,
        public ?string $releaseDate = null,
        public ?float $price = null,
        public mixed $image = null,
        public ?int $manufacturerId = null,
        public ?int $categoryId = null,
        public ?array $maintenances = null,
    ) {
    }
}
