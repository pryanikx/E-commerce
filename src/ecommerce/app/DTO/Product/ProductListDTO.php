<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;
use App\Services\Currency\CurrencyCalculator;
use Illuminate\Support\Facades\Storage;

readonly class ProductListDTO
{

    /**
     * @var int $id
     */
    public int $id;

    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $article
     */
    public string $article;

    /**
     * @var string $manufacturer_name
     */
    public string $manufacturer_name;

    /**
     * @var array $prices
     */
    public array $prices;

    /**
     * @var string|null $image_url
     */
    public ?string $image_url;

    /**
     * @param Product $product
     * @param CurrencyCalculator $calculator
     */
    public function __construct(Product $product, CurrencyCalculator $calculator)
    {
        $this->id = $product->id;
        $this->name = $product->name;
        $this->article = $product->article;
        $this->manufacturer_name = $product->manufacturer->name;
        $this->prices = $product->price ? $calculator->convert((float) $product->price) : null;
        $this->image_url = $product->image_path ? asset($product->image_path) : null;
    }

    /**
     * @return array
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
