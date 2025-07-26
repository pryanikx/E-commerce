<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class ProductsCategoryDTO
{
    /**
     * @param array<int, mixed> $products
     * @param int $currentPage
     * @param int $perPage
     * @param int $total
     * @param int $lastPage
     */
    public function __construct(
        public array $products,
        public int $currentPage,
        public int $perPage,
        public int $total,
        public int $lastPage
    ) {
    }
}
