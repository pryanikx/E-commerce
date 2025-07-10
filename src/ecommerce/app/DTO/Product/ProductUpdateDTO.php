<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

readonly class ProductUpdateDTO
{
    public ?string $name;
    public ?string $article;
    public ?string $description;
    public ?string $release_date;
    public ?float $price;
    public ?\Illuminate\Http\UploadedFile $image;
    public ?int $manufacturer_id;
    public ?int $category_id;
    public ?array $maintenances;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->article = $data['article'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->release_date = $data['release_date'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->manufacturer_id = $data['manufacturer_id'] ?? null;
        $this->category_id = $data['category_id'] ?? null;
        $this->maintenances = $data['maintenances'] ?? null;
    }
}
