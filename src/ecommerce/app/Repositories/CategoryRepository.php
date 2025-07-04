<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Filters\ProductFilter;
use App\Services\Filters\ProductSorter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;


class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @param ProductFilter $productFilter
     * @param ProductSorter $productSorter
     */
    public function __construct(
        protected ProductFilter $productFilter,
        protected ProductSorter $productSorter
    ) {
    }

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
    ): LengthAwarePaginator
    {
        $category = $this->find($id);

        $query = $category->products();

        $query = $this->productFilter->applyFilters($query, $filters);

        $query = $this->productSorter->applySorters($query, $sorters);

        return $query->paginate(self::PER_PAGE, ['*'], 'page', $page);
    }

    /**
     * Apply sorters to the query
     *
     * @param Builder|HasMany $query
     * @param array $sorters
     *
     * @return Builder|HasMany
     */
    public function sort(Builder|HasMany $query, array $sorters): Builder|HasMany
    {
        return $this->productSorter->applySorters($query, $sorters);
    }

    /**
     * Apply filters to the query
     *
     * @param Builder|HasMany $query
     * @param array $filters
     *
     * @return Builder|HasMany
     */
    public function filter(Builder|HasMany $query, array $filters): Builder|HasMany
    {
        return $this->productFilter->applyFilters($query, $filters);
    }
}
