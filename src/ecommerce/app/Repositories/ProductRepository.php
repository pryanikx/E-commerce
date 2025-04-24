<?php

namespace app\Repositories\Eloquent;

use app\Models\Product;
use app\Repositories\Contracts\ProductRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): ?Collection
    {
        return Product::with(['manufacturer'])->get();
    }

    public function find(int $id): ?Product
    {
        return Product::with(['manufacturer', 'category', 'services'])->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function delete(int $id) {
        return Product::destroy($id);
    }
}
