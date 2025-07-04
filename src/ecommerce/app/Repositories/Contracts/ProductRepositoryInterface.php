<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    const PER_PAGE = 20;

    /**
     * Get all products paginated from the database.
     *
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    /**
     * Find a product by ID.
     *
     * @param int $id
     *
     * @return Product
     */
    public function find(int $id): Product;

    /**
     * Create a new product.
     *
     * @param array $data
     *
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     *
     * @return bool
     */
    public function update(Product $product, array $data): bool;

    /**
     * Delete an existing product by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Attach maintenances to a product.
     *
     * @param Product $product
     * @param array $maintenances
     *
     * @return void
     */
    public function attachMaintenances(Product $product, array $maintenances): void;
}
