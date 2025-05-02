<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use \Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): ?Collection;
    public function find(int $id): ?Product;
    public function create(array $data): Product;

    public function update(Product $product, array $data): bool;
    public function delete(int $id): bool;

    public function attachMaintenances(Product $product, array $maintenances): void;
}
