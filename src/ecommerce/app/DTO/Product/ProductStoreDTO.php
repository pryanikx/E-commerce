<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for storing a new product.
 */
readonly class ProductStoreDTO
{
    public string $name;
    public string $article;
    public string $description;
    public string $release_date;
    public float $price;
    public \Illuminate\Http\UploadedFile|string|null $image;
    public int $manufacturer_id;
    public int $category_id;

    /**
     * @var array<int, array<string, float>> $maintenances
     */
    public array $maintenances;

    /**
     * @param array<string, mixed> $data
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
        $this->maintenances = !empty($data['maintenance_ids'])
            ? collect($data['maintenance_ids'])
                ->mapWithKeys(
                    fn ($m) => [(int) $m['id'] => ['price' => (float) $m['price']]]
                )->toArray()
            : [];
    }
}
