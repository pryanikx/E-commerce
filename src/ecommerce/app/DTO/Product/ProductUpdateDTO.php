<?php

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

readonly class ProductUpdateDTO
{
    public ?string $name;
    public ?string $article;
    public ?string $description;
    public ?string $release_date;
    public ?float $price;
    public ?UploadedFile $image;
    public ?int $manufacturer_id;
    public ?int $category_id;
    public ?array $maintenances;

    public function __construct(array $validated)
    {
        Log::debug('ProductUpdateDTO raw validated data', $validated);

        $this->name = $validated['name'] ?? null;
        $this->article = $validated['article'] ?? null;
        $this->description = $validated['description'] ?? null;
        $this->release_date = $validated['release_date'] ?? null;
        $this->price = isset($validated['price']) ? (float) $validated['price'] : null;
        $this->image = $validated['image'] ?? null;
        $this->manufacturer_id = isset($validated['manufacturer_id']) ? (int) $validated['manufacturer_id'] : null;
        $this->category_id = isset($validated['category_id']) ? (int) $validated['category_id'] : null;
        $this->maintenances = isset($validated['maintenance_ids']) && is_array($validated['maintenance_ids'])
            ? collect($validated['maintenance_ids'])->mapWithKeys(function ($m) {
                return [(int) $m['id'] => ['price' => (float) $m['price']]];
            })->toArray()
            : null;

        Log::debug('ProductUpdateDTO constructed', [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'maintenances' => $this->maintenances,
        ]);
    }
}
