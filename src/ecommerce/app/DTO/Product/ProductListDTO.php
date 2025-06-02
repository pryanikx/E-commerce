<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;

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
     * @var float $price
     */
    public float $price;

    /**
     * @var string $image_url
     */
    public string $image_url;

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->id = $product->id;
        $this->name = $product->name;
        $this->article = $product->article;
        $this->manufacturer_name = $product->manufacturer->name;
        $this->price = (float) $product->price;
        $this->image_url = asset($product->image_path);
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
            'price' => $this->price,
            'image_url' => $this->image_url,
        ];
    }
}
