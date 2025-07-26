<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Category\CategoryDTO;
use app\DTO\Category\CategoryStoreDTO;
use app\DTO\Category\CategoryUpdateDTO;
use App\DTO\Category\ProductsCategoryDTO;
use App\Exceptions\DeleteDataException;
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
     * @param CategoryStoreDTO $dto
     *
     * @return CategoryDTO
     */
    public function createCategory(CategoryStoreDTO $dto): CategoryDTO
    {
        $category = $this->categoryRepository->create($dto);

        $this->cacheCategories();

        return $category;
    }

    /**
     * Update an existing category by ID.
     *
     * @param CategoryUpdateDTO $dto
     * @return CategoryDTO
     */
    public function updateCategory(CategoryUpdateDTO $dto): CategoryDTO
    {
        $category = $this->categoryRepository->find($dto->id);

        $dto->name = $dto->name ?? $category->name;
        $dto->alias = $dto->alias ?? Str::slug($dto->name);

        $this->categoryRepository->update($dto);
        $this->cacheCategories();

        return $this->categoryRepository->find($dto->id);
    }

    /**
     * Delete an existing category by ID.
     *
     * @param int $id
     *
     * @return void
     * @throws DeleteDataException
     */
    public function deleteCategory(int $id): void
    {
        if (!$this->categoryRepository->delete($id)) {
            throw new DeleteDataException(__('errors.deletion_failed', ['id' => $id]));
        }

        $this->cacheCategories();
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
