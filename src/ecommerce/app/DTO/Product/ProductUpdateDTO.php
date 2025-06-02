<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

readonly class ProductUpdateDTO
{
    /**
     * @var string|null $name
     */
    public ?string $name;
    /**
     * @var string|null $article
     */
    public ?string $article;
    /**
     * @var string|null $description
     */
    public ?string $description;
    /**
     * @var string|null $release_date
     */
    public ?string $release_date;
    /**
     * @var float|null $price
     */
    public ?float $price;
    /**
     * @var UploadedFile|null $image
     */
    public ?UploadedFile $image;
    /**
     * @var int|null $manufacturer_id
     */
    public ?int $manufacturer_id;
    /**
     * @var int|null $category_id
     */
    public ?int $category_id;
    /**
     * @var array|null $maintenances
     */
    public ?array $maintenances;

    /**
     * @param array $validated
     */

    public function __construct(array $validated)
    {
        $this->name = $validated['name'] ?? null;
        $this->article = $validated['article'] ?? null;
        $this->description = $validated['description'] ?? null;
        $this->release_date = $validated['release_date'] ?? null;
        $this->price = isset($validated['price']) ? (float) $validated['price'] : null;
        $this->image = $validated['image'] instanceof UploadedFile ? $validated['image'] : null;
        $this->manufacturer_id = isset($validated['manufacturer_id']) ? (int) $validated['manufacturer_id'] : null;
        $this->category_id = isset($validated['category_id']) ? (int) $validated['category_id'] : null;
        $this->maintenances = isset($validated['maintenance_ids']) && is_array($validated['maintenance_ids'])
            ? collect($validated['maintenance_ids'])->mapWithKeys(function ($m) {
                return [(int) $m['id'] => ['price' => (float) $m['price']]];
            })->toArray()
            : null;
    }
}
