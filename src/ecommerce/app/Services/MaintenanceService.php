<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;

class MaintenanceService
{
    /**
     * @param MaintenanceRepositoryInterface $maintenanceRepository
     */
    public function __construct(protected MaintenanceRepositoryInterface $maintenanceRepository)
    {
    }

    /**
     * Get all maintenances.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $maintenances = $this->maintenanceRepository->all();

        return $maintenances->map(fn ($maintenance)
            => (new MaintenanceListDTO($maintenance))->toArray())->toArray();
    }

    /**
     * Create new maintenance.
     *
     * @param array $request_validated
     *
     * @return Maintenance
     */
    public function createMaintenance(array $request_validated): Maintenance
    {
        $dto = new MaintenanceStoreDTO($request_validated);

        return $this->maintenanceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'duration' => $dto->duration,
        ]);
    }

    /**
     * Update existing maintenance by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return Maintenance
     */
    public function updateMaintenance(int $id, array $request_validated): Maintenance
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $dto = new MaintenanceUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name !== null ? $dto->name : $maintenance->name,
            'description' => $dto->description !== null ? $dto->description : $maintenance->description,
            'duration' => $dto->duration !== null ? $dto->duration : $maintenance->duration,
        ];

        $this->maintenanceRepository->update($maintenance, $data);

        return $maintenance->refresh();
    }

    /**
     * Delete existing maintenance by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteMaintenance(int $id): bool
    {
        return $this->maintenanceRepository->delete($id);
    }
}
