<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use \Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    public function all(): ?Collection;

    public function find(int $id): ?Service;

    public function delete(int $id): ?bool;

    public function create(array $array);
}
