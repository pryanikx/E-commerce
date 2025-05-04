<?php

namespace App\Repositories;

use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    public function all(): ?Collection
    {
        return Maintenance::all();
    }

    public function find(int $id): Maintenance
    {
        return Maintenance::findOrFail($id);
    }

    public function update(Maintenance $maintenance, array $data): bool
    {
        return $maintenance->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Maintenance::destroy($id);
    }

    public function create(array $array): Maintenance
    {
        return Maintenance::create($array);
    }
}
