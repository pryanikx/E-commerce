<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductStatsDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get statistics for products.
     *
     * @return ProductStatsDTO
     */
    public function getStats(): ProductStatsDTO
    {
        return new ProductStatsDTO(
            totalProducts: Product::count(),
            productsWithImages: Product::whereNotNull('image_path')->count(),
            productsWithManufacturer: Product::whereHas('manufacturer')->count(),
            productsWithCategory: Product::whereHas('category')->count(),
        );
    }
    /**
     * Get all products from the database.
     *
     * @return ProductDTO[]
     */
    public function all(): array
    {
        return Product::with(['manufacturer', 'maintenances'])->get()->map(fn (Product $product)
        => $this->mapToDTO($product))->all();
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     *
     * @return ProductDTO
     */
    public function find(int $id): ProductDTO
    {
        $product = Product::with(['manufacturer', 'category', 'maintenances'])->findOrFail($id);

        return $this->mapToDTO($product);
    }

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $data
     *
     * @return ProductDTO
     */
    public function create(array $data): ProductDTO
    {
        $product = Product::create($data);

        $product->load(['manufacturer', 'category', 'maintenances']);

        return $this->mapToDTO($product);
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $product = Product::findOrFail($id);

        return $product->update($data);
    }

    /**
     * Delete an existing product by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        return (bool) Product::destroy($id);
    }

    /**
     * Attach maintenances to a product.
     *
     * @param int $id
     * @param array<int, mixed> $maintenances
     *
     * @return void
     */
    public function attachMaintenances(int $id, array $maintenances): void
    {
        $product = Product::findOrFail($id);
        $product->maintenances()->sync($maintenances);
    }

    /**
     * Map Eloquent model to DTO.
     *
     * @param Product $product
     *
     * @return ProductDTO
     */
    private function mapToDTO(Product $product): ProductDTO
    {
        $maintenances = null;

        if ($product->relationLoaded('maintenances')) {
            $maintenances = $product->maintenances->map(function ($maintenance) {
                return [
                    'id' => $maintenance->id,
                    'name' => $maintenance->name,
                    'price' => (float) ($maintenance->pivot->price ?? 0),
                    ];
            })->toArray();
        }

        return new ProductDTO(
            id: $product->id,
            name: $product->name,
            article: $product->article,
            description: $product->description,
            releaseDate: (string) $product->release_date,
            price: (float) $product->price,
            imagePath: $product->image_path,
            manufacturerId: $product->manufacturer_id,
            manufacturerName: $product->manufacturer->name ?? '',
            categoryId: $product->category_id,
            categoryName: $product->category->name ?? '',
            maintenances: $maintenances,
        );
    }
}
