<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\DTO\Maintenance\MaintenanceDTO;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class MaintenanceService
{
    private const CACHE_KEY = 'maintenances';

    /**
     * @param MaintenanceRepositoryInterface $maintenanceRepository
     */
    public function __construct(
        private MaintenanceRepositoryInterface $maintenanceRepository,
        private CacheInterface $cache,
    )
    {
    }

    /**
     * Get all maintenances.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        return $this->cache->get(self::CACHE_KEY, function () {
            $maintenances = $this->maintenanceRepository->all();
            return array_map(fn($maintenance) => (new MaintenanceListDTO($maintenance))->toArray(), $maintenances);
        });
    }

    /**
     * Create new maintenance.
     *
     * @param array $request_validated
     *
     * @return MaintenanceDTO
     */
    public function createMaintenance(array $request_validated): MaintenanceDTO
    {
        $dto = new MaintenanceStoreDTO($request_validated);

        $maintenance = $this->maintenanceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'duration' => $dto->duration,
        ]);

        $this->cacheMaintenances();

        return $maintenance;
    }

    /**
     * Update existing maintenance by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return MaintenanceDTO
     */
    public function updateMaintenance(int $id, array $request_validated): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $dto = new MaintenanceUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name ?? $maintenance->name,
            'description' => $dto->description ?? $maintenance->description,
            'duration' => $dto->duration ?? $maintenance->duration,
        ];

        $this->maintenanceRepository->update($id, $data);

        $this->cacheMaintenances();

        return $this->maintenanceRepository->find($id);
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
        $is_deleted = $this->maintenanceRepository->delete($id);

        $this->cacheMaintenances();

        return $is_deleted;
    }

    private function cacheMaintenances(): void
    {
        $maintenances = $this->maintenanceRepository->all();
        $data = array_map(fn($maintenance) => (new MaintenanceListDTO($maintenance))->toArray(), $maintenances);
        $this->cache->put(self::CACHE_KEY, $data);
    }
}
