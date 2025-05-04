<?php

namespace App\Services;

use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceService
{
    public function __construct(protected MaintenanceRepositoryInterface $maintenanceRepository) {}

    public function getAll(): ?Collection
    {
        return $this->maintenanceRepository->all();
    }

    public function createMaintenance(MaintenanceStoreDTO $dto): Maintenance
    {
        return $this->maintenanceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'duration' => $dto->duration,
        ]);
    }

    public function updateMaintenance(int $id, MaintenanceStoreDTO $dto): Maintenance
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $this->maintenanceRepository->update($maintenance, [
            'name' => $dto->name,
            'description' => $dto->description,
            'duration' => $dto->duration,
        ]);

        return $maintenance->refresh();
    }

    public function deleteMaintenance(int $id): bool
    {
        return $this->maintenanceRepository->delete($id);
    }
}
