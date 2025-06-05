<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category\CategoryListDTO;
use App\DTO\Category\CategoryStoreDTO;
use App\DTO\Category\CategoryUpdateDTO;
use App\DTO\Product\ProductListDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Currency\CurrencyCalculator;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CurrencyCalculator $currencyCalculator
     */
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected CurrencyCalculator $currencyCalculator
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
        $categories = $this->categoryRepository->all();

        return $categories->map(fn ($category)
            => (new CategoryListDTO($category))->toArray())->toArray();
    }

    /**
     * Get paginated products of the specified category.
     *
     * @param int $id
     * @param array $filters
     * @param array $sort
     *
     * @return array
     */
    public function getProductsForCategory(int $id, array $filters = [], array $sort = []): array
    {
        $products = $this->categoryRepository->getProductsForCategory($id, $filters, $sort);

        return [
            'data' => $products->map(fn ($product)
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

        return $this->categoryRepository->create([
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);
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
        return $this->categoryRepository->delete($id);
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
}
