<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    /**
     * Get all categoriesfrom the database.
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
     *
     * @return LengthAwarePaginator
     */
    public function getProductsForCategory(int $id): LengthAwarePaginator;
}
