<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;

/**
 * Data transfer object for storing a new product.
 */
readonly class ProductStoreDTO
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
     * @var float $price
     */
    public float $price;

    /**
     * @var UploadedFile|null $image
     */
    public ?UploadedFile $image;

    /**
     * @var int $manufacturer_id
     */
    public int $manufacturer_id;

    /**
     * @var int $category_id
     */
    public int $category_id;

    /**
     * @var array $maintenances
     */
    public array $maintenances;

    /**
     * @param array $validated
     */
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
            ? collect($validated['maintenance_ids'])
                ->mapWithKeys(fn ($m) => [$m['id'] => ['price' => (float) $m['price']]])->toArray()
            : [];
    }
}
