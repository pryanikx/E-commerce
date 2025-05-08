<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @implements CategoryRepositoryInterface<Category>
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get all categories.
     *
     * @return Collection<int, Category>
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find a category by ID.
     *
     * @param int $id
     * @return Category
     */
    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * Create a new category.
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return (bool) Category::destroy($id);
    }

    /**
     * Get paginated products for a category.
     *
     * @param int $id
     * @return LengthAwarePaginator
     */
    public function getProductsForCategory(int $id): LengthAwarePaginator
    {
        $category = $this->find($id);

        return $category->products()->paginate(15);
    }
}
