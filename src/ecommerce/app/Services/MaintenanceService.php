<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Maintenance\MaintenanceDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\Exceptions\DeleteDataException;
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
     * @param MaintenanceStoreDTO $dto
     *
     * @return MaintenanceDTO
     */
    public function createMaintenance(MaintenanceStoreDTO $dto): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->create($dto);

        $this->cacheMaintenances();

        return $maintenance;
    }

    /**
     * Update existing maintenance by ID.
     *
     * @param MaintenanceUpdateDTO $dto
     *
     * @return MaintenanceDTO
     */
    public function updateMaintenance(MaintenanceUpdateDTO $dto): MaintenanceDTO
    {
        $maintenance = $this->maintenanceRepository->find($dto->id);

        $dto->name ??= $maintenance->name;
        $dto->description ??= $maintenance->description;
        $dto->duration ??= $maintenance->duration;

        $this->maintenanceRepository->update($dto);

        $this->cacheMaintenances();

        return $this->maintenanceRepository->find($dto->id);
    }

    /**
     * Delete existing maintenance by ID.
     *
     * @param int $id
     *
     * @return void
     * @throws DeleteDataException
     */
    public function deleteMaintenance(int $id): void
    {
        if (!$this->maintenanceRepository->delete($id)) {
            throw new DeleteDataException(__('errors.deletion_failed', ['id' => $id]));
        }

        $this->cacheMaintenances();
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
