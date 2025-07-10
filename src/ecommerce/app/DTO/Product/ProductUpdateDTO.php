<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

readonly class ProductUpdateDTO
{
    /**
     * @param string|null $name
     * @param string|null $article
     * @param string|null $description
     * @param string|null $release_date
     * @param float|null $price
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param int|null $manufacturer_id
     * @param int|null $category_id
     * @param array|null $maintenances
     */
    public function __construct(
        public ?string $name = null,
        public ?string $article = null,
        public ?string $description = null,
        public ?string $release_date = null,
        public ?float $price = null,
        public ?\Illuminate\Http\UploadedFile $image = null,
        public ?int $manufacturer_id = null,
        public ?int $category_id = null,
        public ?array $maintenances = null,
    ) {}
}
