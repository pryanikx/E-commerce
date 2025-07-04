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

class CategoryService
{
    private const DEFAULT_PAGE_NUMBER = 1;
    private const CACHE_KEY = 'categories';

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CurrencyCalculatorService $currencyCalculator
     */
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected CurrencyCalculatorService $currencyCalculator
    )
    {
    }

    /**
     * Get all categories.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        return cache()->rememberForever(self::CACHE_KEY, function () {
            $categories = $this->categoryRepository->all();

            return $categories->map(fn($category)
                => (new CategoryListDTO($category))->toArray())->toArray();
        });
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
            'data' => $products->map(fn($product)
                => (new ProductListDTO($product, $this->currencyCalculator))->toArray())->toArray(),
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
     * @return Category
     */
    public function createCategory(array $request_validated): Category
    {
        $dto = new CategoryStoreDTO($request_validated);

        $category =  $this->categoryRepository->create([
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);

        $this->cacheCategories();

        return $category;
    }

    /**
     * Update an existing category by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return Category
     */
    public function updateCategory(int $id, array $request_validated): Category
    {
        $category = $this->categoryRepository->find($id);

        $dto = new CategoryUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name ?? $category->name,
            'alias' => $dto->alias ?? ($dto->name ? Str::slug($dto->name) : $category->alias),
        ];

        $this->categoryRepository->update($category, $data);

        $this->cacheCategories();

        return $category->refresh();
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
        $is_deleted =  $this->categoryRepository->delete($id);

        $this->cacheCategories();

        return $is_deleted;
    }

    /**
     * find an existing category by ID.
     *
     * @param int $id
     *
     * @return Category
     */
    public function find(int $id): Category
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

        $data = $categories->map(fn($category) =>
            (new CategoryListDTO($category))->toArray())->toArray();

        cache()->put(self::CACHE_KEY, $data);
    }
}
