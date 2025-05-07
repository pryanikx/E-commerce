<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::all();
    }

    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Category::destroy($id);
    }

    public function getProductsForCategory(int $id): LengthAwarePaginator
    {
        $category = $this->find($id);

        return $category->products()->paginate(15);
    }
}
