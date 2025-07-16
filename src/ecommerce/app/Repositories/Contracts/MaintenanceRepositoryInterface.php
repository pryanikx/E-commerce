<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Maintenance\MaintenanceDTO;

interface MaintenanceRepositoryInterface
{
    /**
     * Get all maintenances from the database.
     *
     * @return MaintenanceDTO[]
     */
    public function all(): array;

    /**
     * Find a maintenance by ID.
     *
     * @param int $id
     *
     * @return MaintenanceDTO
     */
    public function find(int $id): MaintenanceDTO;

    /**
     * Create new maintenance.
     *
     * @param array<string, mixed> $array
     *
     * @return MaintenanceDTO
     */
    public function create(array $array): MaintenanceDTO;

    /**
     * Update existing maintenance.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete maintenance by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
