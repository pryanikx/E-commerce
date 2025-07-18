<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Maintenance\MaintenanceDTO;
use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class MaintenanceService
{
    private const CACHE_KEY = 'maintenances';

    /**
     * @param MaintenanceRepositoryInterface $maintenanceRepository
     * @param CacheInterface $cache
     */
    public function __construct(
        private MaintenanceRepositoryInterface $maintenanceRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * Get all maintenances.
     *
     * @return MaintenanceDTO[]
     */
    public function getAll(): array
    {
        return $this->maintenanceRepository->all();
    }

    /**
     * Create new maintenance.
     *
     * @param array<string, mixed> $requestValidated
     *
     * @return MaintenanceDTO
     */
    public function createMaintenance(array $requestValidated): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->create([
            'name' => $requestValidated['name'],
            'description' => $requestValidated['description'],
            'duration' => $requestValidated['duration'],
        ]);

        $this->cacheMaintenances();

        return $maintenance;
    }

    /**
     * Update existing maintenance by ID.
     *
     * @param int $id
     * @param array<string, mixed> $requestValidated
     *
     * @return MaintenanceDTO
     */
    public function updateMaintenance(int $id, array $requestValidated): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $data = [
            'name' => $requestValidated['name'] ?? $maintenance->name,
            'description' => $requestValidated['description'] ?? $maintenance->description,
            'duration' => $requestValidated['duration'] ?? $maintenance->duration,
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
        $isDeleted = $this->maintenanceRepository->delete($id);

        $this->cacheMaintenances();

        return $isDeleted;
    }

    /**
     * Cache maintenances in storage.
     *
     * @return void
     */
    private function cacheMaintenances(): void
    {
        $maintenances = $this->maintenanceRepository->all();

        $this->cache->put(self::CACHE_KEY, $maintenances);
    }
}
