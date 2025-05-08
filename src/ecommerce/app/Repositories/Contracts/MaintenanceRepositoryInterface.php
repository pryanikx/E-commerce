<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Collection;

interface MaintenanceRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): Maintenance;

    public function update(Maintenance $maintenance, array $data): bool;

    public function delete(int $id): bool;

    public function create(array $array): Maintenance;
}
