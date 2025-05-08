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
    public ?UploadedFile $image;
    public int $manufacturer_id;
    public int $category_id;
    public array $maintenances;

    public function __construct(array $validated)
    {
        $this->name = $validated['name'];
        $this->article = $validated['article'];
        $this->description = $validated['description'];
        $this->release_date = $validated['release_date'];
        $this->price = (float) $validated['price'];
        $this->image = $validated['image'] ?? null;
        $this->manufacturer_id = (int) $validated['manufacturer_id'];
        $this->category_id = (int) $validated['category_id'];
        $this->maintenances = !empty($validated['maintenance_ids'])
            ? collect($validated['maintenance_ids'])->mapWithKeys(fn ($m) => [$m['id'] => ['price' => (float) $m['price']]])->toArray()
            : [];
    }
}
