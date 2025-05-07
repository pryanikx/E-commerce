<?php

namespace App\Services;

use App\DTO\Category\CategoryListDTO;
use App\DTO\Category\CategoryStoreDTO;
use App\DTO\Category\CategoryUpdateDTO;
use App\DTO\Product\ProductListDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}

    public function getAll(): ?array
    {
        $categories = $this->categoryRepository->all();

        return $categories->map(fn($category)
            => (new CategoryListDTO($category))->toArray())->toArray();
    }

    public function getProductsForCategory(int $id): array
    {
        $products = $this->categoryRepository->getProductsForCategory($id);

        return $products->setCollection($products->getCollection()->map(fn($product)
            => (new ProductListDTO($product))->toArray()))->toArray();
    }

    public function createCategory(array $request_validated): Category
    {
        $dto = new CategoryStoreDTO($request_validated);

        return $this->categoryRepository->create([
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);
    }

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

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function find(int $id): Category
    {
        return $this->categoryRepository->find($id);
    }
}
