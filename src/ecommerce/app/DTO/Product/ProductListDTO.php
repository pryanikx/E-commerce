<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Support\Facades\Storage;


readonly class ProductListDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $article
     * @param string $manufacturer_name
     * @param array|null $prices
     * @param string|null $image_url
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $article,
        public string $manufacturer_name,
        public ?array $prices,
        public ?string $image_url,
    ) {}

    /**
     * @return array<string, int|float|string|array|null>
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
