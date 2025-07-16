<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;

/**
 * Data transfer object for showing product.
 */
readonly class ProductShowDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string $description
     * @param string $releaseDate
     * @param string $categoryName
     * @param string $manufacturerName
     * @param array<string, float>|null $prices
     * @param string|null $imageUrl
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $description,
        public string $releaseDate,
        public string $categoryName,
        public string $manufacturerName,
        public ?array $prices,
        public ?string $imageUrl,
        public array $maintenances,
    ) {
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'article' => $this->article,
            'description' => $this->description,
            'release_date' => $this->releaseDate,
            'category_name' => $this->categoryName,
            'manufacturer_name' => $this->manufacturerName,
            'prices' => $this->prices,
            'image_url' => $this->imageUrl,
            'maintenances' => $this->maintenances,
        ];
    }
}
