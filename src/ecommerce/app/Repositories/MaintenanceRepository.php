<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    /**
     * Get all maintenances from the database
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Maintenance::all();
    }

    /**
     * Find a maintenance by ID.
     *
     * @param int $id
     *
     * @return Maintenance
     */
    public function find(int $id): Maintenance
    {
        return Maintenance::findOrFail($id);
    }

    /**
     * Create new maintenance.
     *
     * @param array $array
     *
     * @return Maintenance
     */
    public function create(array $array): Maintenance
    {
        return Maintenance::create($array);
    }

    /**
     * Update existing maintenance.
     *
     * @param Maintenance $maintenance
     * @param array $data
     *
     * @return bool
     */
    public function update(Maintenance $maintenance, array $data): bool
    {
        return $maintenance->update($data);
    }

    /**
     * Delete maintenance by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        return (bool) Maintenance::destroy($id);
    }
}
