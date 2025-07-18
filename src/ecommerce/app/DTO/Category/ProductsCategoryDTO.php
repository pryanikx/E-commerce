<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class ProductsCategoryDTO
{
    /**
     * @param array<int, mixed> $products
     * @param array<string, int> $pagination
     */
    public function __construct(
        public array $products,
        public array $pagination,
    ) {
    }
}
