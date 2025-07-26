<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductStatsDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;

interface ProductRepositoryInterface
{
    /**
     * Get statistics for products.
     *
     * @return ProductStatsDTO
     */
    public function getStats(): ProductStatsDTO;

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
     * @param ProductStoreDTO $dto
     *
     * @return ProductDTO
     */
    public function create(ProductStoreDTO $dto): ProductDTO;

    /**
     * Update an existing product.
     *
     * @param ProductUpdateDTO $dto
     *
     * @return bool
     */
    public function update(ProductUpdateDTO $dto): bool;

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
