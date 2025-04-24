<?php

namespace app\Repositories\Contracts;

use app\Models\Product;
use \Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): ?Collection;
    public function find(int $id): ?Product;
    public function create(array $data): Product;
    public function delete(int $id);
}
