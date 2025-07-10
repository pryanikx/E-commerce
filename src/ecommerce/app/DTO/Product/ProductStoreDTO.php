<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

readonly class ProductStoreDTO
{
    public string $name;
    public string $article;
    public string $description;
    public string $release_date;
    public float $price;
    public ?\Illuminate\Http\UploadedFile $image;
    public int $manufacturer_id;
    public int $category_id;
    public array $maintenances;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->article = $data['article'];
        $this->description = $data['description'];
        $this->release_date = $data['release_date'];
        $this->price = $data['price'];
        $this->image = $data['image'] ?? null;
        $this->manufacturer_id = $data['manufacturer_id'];
        $this->category_id = $data['category_id'];
        $this->maintenances = $data['maintenances'] ?? [];
    }
}
