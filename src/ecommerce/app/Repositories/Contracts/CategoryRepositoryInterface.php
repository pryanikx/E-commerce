<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    const PER_PAGE = 15;
    const DEFAULT_PAGE_NUMBER = 1;

    /**
     * Get all categories from the database.
     *
     * @return Collection<int, Category>
     */
    public function all(): Collection;

    /**
     * Find an existing category by ID.
     *
     * @param int $id
     *
     * @return Category
     */
    public function find(int $id): Category;

    /**
     * Create a new category.
     *
     * @param array<string, mixed> $data
     *
     * @return Category
     */
    public function create(array $data): Category;

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     *
     * @return bool
     */
    public function update(Category $category, array $data): bool;

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
     * @param array $filters
     * @param array $sorters
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function getProductsForCategory(
        int $id,
        array $filters = [],
        array $sorters = [],
        int $page = self::DEFAULT_PAGE_NUMBER
    ): LengthAwarePaginator;

    /**
     * Apply sorters to the query
     *
     * @param Builder|HasMany $query
     * @param array $sorters
     *
     * @return Builder|HasMany
     */
    public function sort(Builder|HasMany $query, array $sorters): Builder|HasMany;

    /**
     * Apply filters to the query
     *
     * @param Builder|HasMany $query
     * @param array $filters
     *
     * @return Builder|HasMany
     */
    public function filter(Builder|HasMany $query, array $filters): Builder|HasMany;
}
