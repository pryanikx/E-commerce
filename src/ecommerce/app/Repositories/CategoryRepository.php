<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 *
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    public const PER_PAGE = 15;

    public const DEFAULT_PAGE = 1;

    public const DEFAULT_SORT_COLUMN = 'id';

    public const SORT_COLUMNS = ['price', 'release_date', 'id'];

    public const DEFAULT_SORT_ORDER = 'asc';

    public const SORT_ORDERS = ['asc', 'desc'];

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
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function getProductsForCategory(
        int $id,
        array $filters = [],
        array $sort = [],
        int $page = self::DEFAULT_PAGE
    ): LengthAwarePaginator
    {
        $category = $this->find($id);

        $query = $category->products();

        $query = $this->filter($query, $filters);

        $query = $this->sort($query, $sort);

        return $query->paginate(self::PER_PAGE, ['*'], 'page', $page);
    }

    /**
     * Apply sorters to the query
     *
     * @param $query
     * @param array $sort
     *
     * @return mixed
     */
    public function sort($query, array $sort): mixed
    {
        $sortBy = in_array(
            $sort['sort_by'] ?? self::DEFAULT_SORT_COLUMN,
            self::SORT_COLUMNS
        ) ? $sort['sort_by'] : self::DEFAULT_SORT_COLUMN;

        $sortOrder = in_array(
            $sort['sort_order'] ?? self::DEFAULT_SORT_ORDER,
            self::SORT_ORDERS
        ) ? $sort['sort_order'] : self::DEFAULT_SORT_ORDER;

        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }

    /**
     * Apply filters to the query
     *
     * @param $query
     * @param array $filters
     *
     * @return mixed
     */
    public function filter($query, array $filters): mixed
    {
        if (!empty($filters['manufacturer_id'])) {
            $query->where('manufacturer_id', (int) $filters['manufacturer_id']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        return $query;
    }
}
