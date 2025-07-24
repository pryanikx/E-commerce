<?php

declare(strict_types=1);

namespace App\DTO\Product;

class ProductStatsDTO
{
    /**
     * @param int $totalProducts
     * @param int $productsWithImages
     * @param int $productsWithManufacturer
     * @param int $productsWithCategory
     */
    public function __construct(
        public int $totalProducts,
        public int $productsWithImages,
        public int $productsWithManufacturer,
        public int $productsWithCategory,
    ) {
    }
}
