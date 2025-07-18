<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Category\CategoryDTO;
use App\DTO\Category\ProductsCategoryDTO;
use App\DTO\Product\ProductListDTO;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Filters\ProductFilter;
use App\Services\Filters\ProductSorter;
use Illuminate\Database\Eloquent\Builder;
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
     * @return CategoryDTO[]
     */
    public function all(): array
    {
        return Category::all()->map(fn (Category $category)
        => $this->mapToDTO($category))->all();
    }

    /**
     * Find an existing category by ID.
     *
     * @param int $id
     *
     * @return CategoryDTO
     */
    public function find(int $id): CategoryDTO
    {
        $category = Category::findOrFail($id);

        return $this->mapToDTO($category);
    }

    /**
     * Create a new category.
     *
     * @param array<string, mixed> $data
     *
     * @return CategoryDTO
     */
    public function create(array $data): CategoryDTO
    {
        $category = Category::create($data);

        return $this->mapToDTO($category);
    }

    /**
     * Update an existing category.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $category = Category::findOrFail($id);

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
    ): ProductsCategoryDTO {
        $category = Category::findOrFail($id);

        $query = $category->products();

        $query = $this->productFilter->applyFilters($query, $filters);

        $query = $this->productSorter->applySorters($query, $sorters);

        $products = $query->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return $this->mapPaginateToDTO($products);
    }

    /**
     * Apply sorters to the query.
     *
     * @param Builder<Product>|HasMany<Product, \Illuminate\Database\Eloquent\Model> $query
     * @param array<string, string> $sorters
     *
     * @return Builder<Product>|HasMany<Product, \Illuminate\Database\Eloquent\Model>
     */
    public function sort(Builder|HasMany $query, array $sorters): Builder|HasMany
    {
        return $this->productSorter->applySorters($query, $sorters);
    }

    /**
     * Apply filters to the query.
     *
     * @param Builder<Product>|HasMany<Product, \Illuminate\Database\Eloquent\Model> $query
     * @param array<string, mixed> $filters
     *
     * @return Builder<Product>|HasMany<Product, \Illuminate\Database\Eloquent\Model>
     */
    public function filter(Builder|HasMany $query, array $filters): Builder|HasMany
    {
        return $this->productFilter->applyFilters($query, $filters);
    }

    /**
     * Map Eloquent model to DTO.
     *
     * @param Category $category
     *
     * @return CategoryDTO
     */
    public function mapToDTO(Category $category): CategoryDTO
    {
        return new CategoryDTO(
            $category->id,
            $category->name,
            $category->alias,
        );
    }

    /**
     * @param LengthAwarePaginator<int, Product> $products
     *
     * @return ProductsCategoryDTO
     */
    public function mapPaginateToDTO(LengthAwarePaginator $products): ProductsCategoryDTO
    {
        $productsDTO = $products->map(fn ($product) => (new ProductListDTO(
            id: $product->id,
            name: $product->name,
            article: $product->article,
            manufacturerName: $product->manufacturer->name ?? '',
            price: $product->price,
            imageUrl: $product->image_path ? asset($product->image_path) : null,
        ))->toArray());

        $pagination = [
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'last_page' => $products->lastPage(),
        ];

        return
            (new ProductsCategoryDTO(
                products: $productsDTO->toArray(),
                pagination: $pagination,
            ));
    }
}
