<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category\CategoryDTO;
use App\DTO\Category\ProductsCategoryDTO;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Support\Str;

class CategoryService
{
    private const DEFAULT_PAGE_NUMBER = 1;
    private const CACHE_KEY = 'categories';

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CacheInterface $cache
     */
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Get all categories.
     *
     * @return CategoryDTO[]
     */
    public function getAll(): array
    {
        return $this->categoryRepository->all();
    }

    /**
     * Get paginated products of the specified category.
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
        return $this->categoryRepository->getProductsForCategory($id, $filters, $sorters, $page);
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
        $category = $this->categoryRepository->create([
            'name' => $requestValidated['name'],
            'alias' => $requestValidated['alias'],
        ]);

        $this->cacheCategories();

        return $category;
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

        $data = [
            'name' => $requestValidated['name'] ?? $category->name,
            'alias' => $requestValidated['alias'] ??
                ($requestValidated['name'] ? Str::slug($requestValidated['name']) : $category->alias),
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

        $this->cache->put(self::CACHE_KEY, $categories);
    }
}
