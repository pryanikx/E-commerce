<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category\CategoryDTO;
use App\DTO\Category\CategoryListDTO;
use App\DTO\Category\CategoryStoreDTO;
use App\DTO\Category\CategoryUpdateDTO;
use App\DTO\Product\ProductListDTO;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Support\Str;

class CategoryService
{
    private const DEFAULT_PAGE_NUMBER = 1;
    private const CACHE_KEY = 'categories';

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CurrencyCalculatorService $currencyCalculator
     * @param CacheInterface $cache
     */
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CurrencyCalculatorService $currencyCalculator,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Get all categories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $categories = $this->categoryRepository->all();

        return array_map(fn ($category) =>
        $this->makeCategoryListDTO($category)->toArray(), $categories);
    }

    /**
     * Get paginated products of the specified category.
     *
     * @param int $id
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorters
     * @param int $page
     *
     * @return array<string, mixed>
     */
    public function getProductsForCategory(
        int $id,
        array $filters = [],
        array $sorters = [],
        int $page = self::DEFAULT_PAGE_NUMBER
    ): array {
        $products = $this->categoryRepository->getProductsForCategory($id, $filters, $sorters, $page);

        return [
            'products' => $products->map(fn ($product) =>
            $this->makeProductListDTO($product)->toArray())->toArray(),

            'pagination' => [
                'currentPage' => $products->currentPage(),
                'perPage' => $products->perPage(),
                'total' => $products->total(),
                'lastPage' => $products->lastPage(),
            ],
        ];
    }

    /**
     * Create a new category.
     *
     * @param array<string, string> $requestValidated
     *
     * @return CategoryDTO
     */
    public function createCategory(array $requestValidated): CategoryDTO
    {
        $dto = new CategoryStoreDTO($requestValidated);

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
     * @param array<string, string> $requestValidated
     *
     * @return CategoryDTO
     */
    public function updateCategory(int $id, array $requestValidated): CategoryDTO
    {
        $category = $this->categoryRepository->find($id);

        $dto = new CategoryUpdateDTO($requestValidated);

        $data = [
            'name' => $dto->name ?? $category->name,
            'alias' => $dto->alias ?? ($dto->name ? Str::slug($dto->name) : $category->alias),
        ];

        $this->categoryRepository->update($id, $data);

        $this->cacheCategories();

        return $this->categoryRepository->find($id);
    }

    /**
     * Delete an existing category by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        $isDeleted = $this->categoryRepository->delete($id);

        $this->cacheCategories();

        return $isDeleted;
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
        return $this->categoryRepository->find($id);
    }

    /**
     * Cache categories in storage.
     *
     * @return void
     */
    private function cacheCategories(): void
    {
        $categories = $this->categoryRepository->all();
        $data = array_map(fn ($category) => $this->makeCategoryListDTO($category)->toArray(), $categories);
        $this->cache->put(self::CACHE_KEY, $data);
    }

    /**
     * Convert a Product model to ProductListDTO.
     *
     * @param Product $product
     *
     * @return ProductListDTO
     */
    private function makeProductListDTO(Product $product): ProductListDTO
    {
        return new ProductListDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->manufacturer->name,
            $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            $product->image_path ? asset($product->image_path) : null,
        );
    }

    /**
     * Make DTO for categories.
     *
     * @param mixed $category
     *
     * @return CategoryListDTO
     */
    private function makeCategoryListDTO(mixed $category): CategoryListDTO
    {
        if ($category instanceof CategoryListDTO) {
            return $category;
        }
        if ($category instanceof CategoryDTO) {
            return new CategoryListDTO($category->id, $category->name, $category->alias);
        }
        return new CategoryListDTO(
            $category['id'] ?? $category->id,
            $category['name'] ?? $category->name,
            $category['alias'] ?? $category->alias
        );
    }
}
