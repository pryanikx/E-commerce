<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

class ProductUpdateDTO
{
    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $article
     * @param string|null $description
     * @param string|null $releaseDate
     * @param float|array<string, float> $price ,
     * @param UploadedFile|mixed|null $image
     * @param int|null $manufacturerId
     * @param int|null $categoryId
     * @param array<int, mixed>|null $maintenances
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $article,
        public ?string $description,
        public ?string $releaseDate,
        public float|array|null $price,
        public mixed $image,
        public ?int $manufacturerId,
        public ?int $categoryId,
        public ?array $maintenances,
    ) {
    }

    /**
     * Transform a dto object to array.
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
            'price' => $this->price,
            'image_url' => $this->image,
            'manufacturer_id' => $this->manufacturerId,
            'category_id' => $this->categoryId,
            'maintenances' => $this->maintenances,
        ];
    }
}
