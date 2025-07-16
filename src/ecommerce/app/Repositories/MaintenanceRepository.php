<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Maintenance\MaintenanceDTO;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    /**
     * Get all maintenances from the database.
     *
     * @return MaintenanceDTO[]
     */
    public function all(): array
    {
        return Maintenance::all()->map(fn (Maintenance $maintenance) =>
            $this->mapToDTO($maintenance))->all();
    }

    /**
     * Find a maintenance by ID.
     *
     * @param int $id
     *
     * @return MaintenanceDTO
     */
    public function find(int $id): MaintenanceDTO
    {
        $maintenance = Maintenance::findOrFail($id);

        return $this->mapToDTO($maintenance);
    }

    /**
     * Create new maintenance.
     *
     * @param array<string, mixed> $array
     *
     * @return MaintenanceDTO
     */
    public function create(array $array): MaintenanceDTO
    {
        $maintenance = Maintenance::create($array);

        return $this->mapToDTO($maintenance);
    }

    /**
     * Update existing maintenance.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $maintenance = Maintenance::findOrFail($id);

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

    /**
     * Map Eloquent model to DTO.
     *
     * @param Maintenance $maintenance
     *
     * @return MaintenanceDTO
     */
    private function mapToDTO(Maintenance $maintenance): MaintenanceDTO
    {
        return new MaintenanceDTO(
            $maintenance->id,
            $maintenance->name,
            $maintenance->description,
            $maintenance->duration,
        );
    }
}
