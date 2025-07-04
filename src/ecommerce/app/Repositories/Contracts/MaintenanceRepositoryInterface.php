<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Collection;

interface MaintenanceRepositoryInterface
{
    /**
     * Get all maintenances from the database
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a maintenance by ID.
     *
     * @param int $id
     *
     * @return Maintenance
     */
    public function find(int $id): Maintenance;

    /**
     * @param array $array
     *
     * @return Maintenance
     */
    public function create(array $array): Maintenance;

    /**
     * Update existing maintenance.
     *
     * @param Maintenance $maintenance
     * @param array $data
     *
     * @return bool
     */
    public function update(Maintenance $maintenance, array $data): bool;

    /**
     * Delete maintenance by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
