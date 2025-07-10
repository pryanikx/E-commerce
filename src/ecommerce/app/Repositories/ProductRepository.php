<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Product\ProductDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products from the database.
     *
     * @return ProductDTO[]
     */
    public function all(): array
    {
        return Product::with(['manufacturer'])->get()->map(fn(Product $product)
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
     * @param array $data
     *
     * @return ProductDTO
     */
    public function create(array $data): ProductDTO
    {
        $product = Product::create($data);

        return $this->mapToDTO($product);
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array $data
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
     * @param array $maintenances
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
        return new ProductDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->description,
            $product->release_date?->toDateString(),
            (float)$product->price,
            $product->image_path,
            $product->manufacturer_id,
            $product->manufacturer?->name,
            $product->category_id,
            $product->category?->name,
            $product->created_at?->toISOString() ?? '',
            $product->updated_at?->toISOString() ?? '',
        );
    }
}
