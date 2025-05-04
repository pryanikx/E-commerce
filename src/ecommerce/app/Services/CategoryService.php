<?php

namespace App\Services;

use App\DTO\Category\CategoryStoreDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}

    public function getAll(): ?Collection
    {
        return $this->categoryRepository->all();
    }

    public function find(int $id): Category
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(CategoryStoreDTO $dto): Category
    {
        return $this->categoryRepository->create([
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);
    }

    public function updateCategory(int $id, CategoryStoreDTO $dto): Category
    {
        $category = $this->categoryRepository->find($id);

        $this->categoryRepository->update($category, [
            'name' => $dto->name,
            'alias' => $dto->alias,
        ]);

        return $category->refresh();
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
