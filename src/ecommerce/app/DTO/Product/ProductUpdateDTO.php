<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for updating a product.
 */
readonly class ProductUpdateDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $article = null,
        public ?string $description = null,
        public ?string $release_date = null,
        public ?float $price = null,
        public mixed $image = null,
        public ?int $manufacturer_id = null,
        public ?int $category_id = null,
        public ?array $maintenances = null,
    ) {}
}
