<?php

namespace App\Services;

use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceService
{
    public function __construct(protected MaintenanceRepositoryInterface $maintenanceRepository) {}

    public function getAll(): ?array
    {
        $maintenances = $this->maintenanceRepository->all();

        return $maintenances->map(fn($maintenance)
            => (new MaintenanceListDTO($maintenance))->toArray())->toArray();
    }

    public function createMaintenance(array $request_validated): Maintenance
    {
        $dto = new MaintenanceStoreDTO($request_validated);

        return $this->maintenanceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'duration' => $dto->duration,
        ]);
    }

    public function updateMaintenance(int $id, array $request_validated): Maintenance
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $dto = new MaintenanceUpdateDTO($request_validated);

        $data = array_filter([
            'name' => $dto->name !== null ? $dto->name : $maintenance->name,
            'description' => $dto->description !== null ? $dto->description : $maintenance->description,
            'duration' => $dto->duration !== null ? $dto->duration : $maintenance->duration,
        ], fn($value) => $value !== null);

        if (!empty($data)) {
            $this->maintenanceRepository->update($maintenance, $data);
        }

        return $maintenance->refresh();
    }

    public function deleteMaintenance(int $id): bool
    {
        return $this->maintenanceRepository->delete($id);
    }
}
