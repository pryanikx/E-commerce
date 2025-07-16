<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Product\ProductDTO;

interface ProductRepositoryInterface
{
    /**
     * Get all products from the database.
     *
     * @return ProductDTO[]
     */
    public function all(): array;

    /**
     * Find a product by ID.
     *
     * @param int $id
     *
     * @return ProductDTO
     */
    public function find(int $id): ProductDTO;

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $data
     *
     * @return ProductDTO
     */
    public function create(array $data): ProductDTO;

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool;

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
     * @param int $id
     * @param array<int, mixed> $maintenances
     *
     * @return void
     */
    public function attachMaintenances(int $id, array $maintenances): void;
}
