<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

readonly class ProductStoreDTO
{
    /**
     * @param string $name
     * @param string $article
     * @param string $description
     * @param string $release_date
     * @param float $price
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param int $manufacturer_id
     * @param int $category_id
     * @param array $maintenances
     */
    public function __construct(
        public string $name,
        public string $article,
        public string $description,
        public string $release_date,
        public float $price,
        public ?\Illuminate\Http\UploadedFile $image,
        public int $manufacturer_id,
        public int $category_id,
        public array $maintenances,
    ) {}
}
