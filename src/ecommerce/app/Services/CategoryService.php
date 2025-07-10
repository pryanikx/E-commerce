<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category\CategoryListDTO;
use App\DTO\Category\CategoryStoreDTO;
use App\DTO\Category\CategoryUpdateDTO;
use App\DTO\Product\ProductListDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class CategoryService
{
    private const DEFAULT_PAGE_NUMBER = 1;
    private const CACHE_KEY = 'categories';

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CurrencyCalculatorService $currencyCalculator
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CurrencyCalculatorService $currencyCalculator,
        private CacheInterface $cache,
    )
    {
    }

    /**
     * Get all categories.
     *
     * @return array
     */
    public function getAll(): ?array
    {
        $categories = $this->categoryRepository->all();
        return [
            'data' => array_map(fn($category) =>
                $this->makeCategoryListDTO($category)->toArray(), $categories),
        ];
    }

    /**
     * Get paginated products of the specified category.
     *
     * @param int $id
     * @param array $filters
     * @param array $sorters
     * @param int $page
     *
     * @return array
     */
    public function getProductsForCategory(
        int $id,
        array $filters = [],
        array $sorters = [],
        int $page = self::DEFAULT_PAGE_NUMBER
    ): array
    {
        $products = $this->categoryRepository->getProductsForCategory($id, $filters, $sorters, $page);

        return [
            'data' => $products->map(fn($product) => $this->makeProductListDTO($product)->toArray())->toArray(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ];
    }

    /**
     * Create a new category.
     *
     * @param array $request_validated
     *
     * @return \App\DTO\Category\CategoryDTO
     */
    public function createCategory(array $request_validated): \App\DTO\Category\CategoryDTO
    {
        $dto = new CategoryStoreDTO($request_validated);

        $categoryDTO = $this->categoryRepository->create([
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);

        $this->cacheCategories();

        return $categoryDTO;
    }

    /**
     * Update an existing category by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return \App\DTO\Category\CategoryDTO
     */
    public function updateCategory(int $id, array $request_validated): \App\DTO\Category\CategoryDTO
    {
        $category = $this->categoryRepository->find($id);

        $dto = new CategoryUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name ?? $category->name,
            'alias' => $dto->alias ?? ($dto->name ? \Illuminate\Support\Str::slug($dto->name) : $category->alias),
        ];

        $this->categoryRepository->update($id, $data);

        $this->cacheCategories();

        return $this->categoryRepository->find($id);
    }

    /**
     * delete an existing category by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        $is_deleted = $this->categoryRepository->delete($id);

        $this->cacheCategories();

        return $is_deleted;
    }

    /**
     * find an existing category by ID.
     *
     * @param int $id
     *
     * @return \App\DTO\Category\CategoryDTO
     */
    public function find(int $id): CategoryDTO
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Save categories in cache
     *
     * @return void
     */
    private function cacheCategories(): void
    {
        $categories = $this->categoryRepository->all();
        $data = array_map(fn($category) => $this->makeCategoryListDTO($category)->toArray(), $categories);
        $this->cache->put(self::CACHE_KEY, $data);
    }

    /**
     * Convert Product model to ProductListDTO.
     *
     * @param \App\Models\Product $product
     * 
     * @return \App\DTO\Product\ProductListDTO
     */
    private function makeProductListDTO(\App\Models\Product $product): \App\DTO\Product\ProductListDTO
    {
        return new \App\DTO\Product\ProductListDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->manufacturer->name,
            $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            $product->image_path ? asset($product->image_path) : null,
        );
    }

    /**
     * Make DTO for categories
     * 
     * @param mixed $maintenance
     * 
     * @return CategoryListDTO
     */
    private function makeCategoryListDTO($category): \App\DTO\Category\CategoryListDTO
    {
        if ($category instanceof \App\DTO\Category\CategoryListDTO) {
            return $category;
        }
        if ($category instanceof \App\DTO\Category\CategoryDTO) {
            return new CategoryListDTO($category->id, $category->name, $category->alias);
        }
        return new CategoryListDTO($category['id'] ?? $category->id, $category['name'] ?? $category->name, $category['alias'] ?? $category->alias);
    }
}
