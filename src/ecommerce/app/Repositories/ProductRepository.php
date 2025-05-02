<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): ?Collection
    {
        return Product::with(['manufacturer'])->get();
    }

    public function find(int $id): ?Product
    {
        return Product::with(['manufacturer', 'category', 'maintenance'])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(int $id): bool {
        // Do I need to detach() all services for product, if product is ON DELETE CASCADE?
        return Product::destroy($id);
    }

    public function attachMaintenances(Product $product, array $maintenances): void
    {
        if (!empty($maintenances)) {
            $product->maintenances()->sync($maintenances);
        }
    }
}
