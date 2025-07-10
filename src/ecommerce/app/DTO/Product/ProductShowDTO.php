<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Support\Facades\Storage;

readonly class ProductShowDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string $description
     * @param string $release_date
     * @param string $category_name
     * @param string $manufacturer_name
     * @param array|null $prices
     * @param string|null $image_url
     * @param array $maintenances
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $description,
        public string $release_date,
        public string $category_name,
        public string $manufacturer_name,
        public ?array $prices,
        public ?string $image_url,
        public array $maintenances,
    ) {}

    /**
     * Convert DTO to array.
     *
     * @return array<string, int|string|array|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'article' => $this->article,
            'description' => $this->description,
            'release_date' => $this->release_date,
            'category_name' => $this->category_name,
            'manufacturer_name' => $this->manufacturer_name,
            'prices' => $this->prices,
            'image_url' => $this->image_url,
            'maintenances' => $this->maintenances,
        ];
    }
}
