<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Category\CategoryDTO;
use App\DTO\Category\ProductsCategoryDTO;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    const PER_PAGE = 15;
    const DEFAULT_PAGE_NUMBER = 1;

    /**
     * Get all categories from the database.
     *
     * @return CategoryDTO[]
     */
    public function all(): array;

    /**
     * Find an existing category by ID.
     *
     * @param int $id
     *
     * @return CategoryDTO
     */
    public function find(int $id): CategoryDTO;

    /**
     * Create a new category.
     *
     * @param array<string, mixed> $data
     *
     * @return CategoryDTO
     */
    public function create(array $data): CategoryDTO;

    /**
     * Update an existing category.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a category by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get paginated products for a category.
     *
     * @param int $id
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorters
     * @param int $page
     *
     * @return ProductsCategoryDTO
     */
    public function getProductsForCategory(
        int $id,
        array $filters = [],
        array $sorters = [],
        int $page = self::DEFAULT_PAGE_NUMBER
    ): ProductsCategoryDTO;

    /**
     * Map Eloquent model to DTO.
     *
     * @param Category $category
     *
     * @return CategoryDTO
     */
    public function mapToDTO(Category $category): CategoryDTO;

    /**
     * @param LengthAwarePaginator<int, Product> $products
     *
     * @return ProductsCategoryDTO
     */
    public function mapPaginateToDTO(LengthAwarePaginator $products): ProductsCategoryDTO;
}
