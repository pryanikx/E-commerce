<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Maintenance\MaintenanceDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;

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
     * @param MaintenanceStoreDTO $dto
     *
     * @return MaintenanceDTO
     */
    public function create(MaintenanceStoreDTO $dto): MaintenanceDTO;

    /**
     * Update existing maintenance.
     *
     * @param MaintenanceUpdateDTO $dto
     *
     * @return bool
     */
    public function update(MaintenanceUpdateDTO $dto): bool;

    /**
     * Delete maintenance by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
