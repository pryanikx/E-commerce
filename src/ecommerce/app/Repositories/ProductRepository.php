<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products paginated from the database.
     *
     * @param int $pageNumber
     *
     * @return LengthAwarePaginator;
     */
    public function all(int $pageNumber): LengthAwarePaginator
    {
        return Product::with(['manufacturer'])->paginate(20, ['*'], 'page', $pageNumber);
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     *
     * @return Product
     */
    public function find(int $id): Product
    {
        return Product::with(['manufacturer', 'category', 'maintenances'])->findOrFail($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     *
     * @return Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     *
     * @return bool
     */
    public function update(Product $product, array $data): bool
    {
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
     * @param Product $product
     * @param array $maintenances
     *
     * @return void
     */
    public function attachMaintenances(Product $product, array $maintenances): void
    {
        $product->maintenances()->sync($maintenances);
    }
}
