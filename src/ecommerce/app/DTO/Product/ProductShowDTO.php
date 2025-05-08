<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\Models\Product;

readonly class ProductShowDTO
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $article
     */
    public string $article;

    /**
     * @var string $description
     */
    public string $description;

    /**
     * @var string $release_date
     */
    public string $release_date;

    /**
     * @var string $category_name
     */
    public string $category_name;

    /**
     * @var string $manufacturer_name
     */
    public string $manufacturer_name;

    /**
     * @var float $price
     */
    public float $price;

    /**
     * @var string|null $image_url
     */
    public ?string $image_url;

    /**
     * @var array $maintenances
     */
    public array $maintenances;

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->name = $product->name;
        $this->article = $product->article;
        $this->description = $product->description;
        $this->release_date = $product->release_date;
        $this->category_name = $product->category->name;
        $this->manufacturer_name = $product->manufacturer->name;
        $this->price = $product->price;
        $this->image_url = $product->image_path ? asset($product->image_path) : null;
        $this->maintenances = $product->maintenances->map(fn ($maintenance) => [
            'name' => $maintenance->name,
            'price' => (float) $maintenance->pivot->price,
        ])->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'article' => $this->article,
            'description' => $this->description,
            'release_date' => $this->release_date,
            'category_name' => $this->category_name,
            'manufacturer_name' => $this->manufacturer_name,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'maintenances' => $this->maintenances,
        ];
    }
}
