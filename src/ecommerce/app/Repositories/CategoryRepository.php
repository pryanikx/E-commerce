<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get all categories from the database.
     *
     * @return Collection<int, Category>
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find an existing category by ID.
     *
     * @param int $id
     *
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
     *
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
     * @param array $data
     *
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
     *
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
     * @param array $filters
     * @param array $sort
     *
     * @return LengthAwarePaginator
     */
    public function getProductsForCategory(int $id, array $filters = [], array $sort = []): LengthAwarePaginator
    {
        $category = $this->find($id);

        $query = $category->products();

        if (!empty($filters['manufacturer_id'])) {
            $query->where('manufacturer_id', (int) $filters['manufacturer_id']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        $sortBy = in_array($sort['sort_by'] ?? 'id', ['price', 'release_date', 'id']) ? $sort['sort_by'] : 'id';
        $sortOrder = in_array($sort['sort_order'] ?? 'asc', ['asc', 'desc']) ? $sort['sort_order'] : 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(15);
    }
}
