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
     * @param string $manufacturer_name
     * @param array<string, float>|null $prices
     * @param string|null $image_url
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $manufacturer_name,
        public ?array $prices,
        public ?string $image_url,
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
            'manufacturer_name' => $this->manufacturer_name,
            'prices' => $this->prices,
            'image_url' => $this->image_url,
        ];
    }
}
