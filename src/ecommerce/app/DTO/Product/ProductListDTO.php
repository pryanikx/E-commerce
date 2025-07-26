<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for listing products.
 */
readonly class ProductListDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string $manufacturerName
     * @param float|null $price
     * @param string|null $imageUrl
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $manufacturerName,
        public ?float $price,
        public ?string $imageUrl,
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
            'manufacturer_name' => $this->manufacturerName,
            'price' => $this->price,
            'image_url' => $this->imageUrl,
        ];
    }
}
