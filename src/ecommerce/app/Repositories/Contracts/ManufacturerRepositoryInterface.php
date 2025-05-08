<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Collection;

interface ManufacturerRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): Manufacturer;

    public function update(Manufacturer $manufacturer, array $data): bool;

    public function delete(int $id): bool;

    public function create(array $array): Manufacturer;
}
