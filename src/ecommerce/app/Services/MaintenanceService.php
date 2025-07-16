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
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $maintenances = $this->maintenanceRepository->all();
        return array_map(fn ($maintenance) => $this->makeMaintenanceListDTO($maintenance)->toArray(), $maintenances);
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
        $dto = new MaintenanceStoreDTO($requestValidated);

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
     * @param array<string, mixed> $requestValidated
     *
     * @return MaintenanceDTO
     */
    public function updateMaintenance(int $id, array $requestValidated): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->find($id);

        $dto = new MaintenanceUpdateDTO($requestValidated);

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
        $data = array_map(fn ($maintenance) => $this->makeMaintenanceListDTO($maintenance)->toArray(), $maintenances);
        $this->cache->put(self::CACHE_KEY, $data);
    }

    /**
     * Make DTO for maintenances.
     *
     * @param mixed $maintenance
     *
     * @return MaintenanceListDTO
     */
    private function makeMaintenanceListDTO(mixed $maintenance): MaintenanceListDTO
    {
        if ($maintenance instanceof MaintenanceListDTO) {
            return $maintenance;
        }

        if ($maintenance instanceof MaintenanceDTO) {
            return new MaintenanceListDTO(
                $maintenance->id,
                $maintenance->name,
                $maintenance->description,
                $maintenance->duration
            );
        }

        return new MaintenanceListDTO(
            $maintenance['id'] ?? $maintenance->id,
            $maintenance['name'] ?? $maintenance->name,
            $maintenance['description'] ?? $maintenance->description,
            $maintenance['duration'] ?? $maintenance->duration
        );
    }
}
