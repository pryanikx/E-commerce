<?php

declare(strict_types=1);

namespace App\DTO\Product;

/**
 * Data transfer object for updating a product.
 */
readonly class ProductUpdateDTO
{
    public ?string $name;
    public ?string $article;
    public ?string $description;
    public ?string $release_date;
    public ?float $price;
    public \Illuminate\Http\UploadedFile|string|null $image;
    public ?int $manufacturer_id;
    public ?int $category_id;

    /**
     * @var array<int, array<string, float>>|null $maintenances
     */
    public ?array $maintenances;

    /**
     * @param array<string, mixed> $data
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
        $this->maintenances = isset($data['maintenance_ids']) && is_array($data['maintenance_ids'])
            ? collect($data['maintenance_ids'])
                ->mapWithKeys(
                    fn ($m) => [(int) $m['id'] => ['price' => (float) $m['price']]]
                )->toArray()
            : null;
    }
}
