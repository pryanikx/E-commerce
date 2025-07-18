<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerDTO;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class ManufacturerService
{
    private const CACHE_KEY = 'manufacturers';

    /**
     * @param ManufacturerRepositoryInterface $manufacturerRepository
     * @param CacheInterface $cache
     */
    public function __construct(
        private readonly ManufacturerRepositoryInterface $manufacturerRepository,
        private readonly CacheInterface                  $cache,
    ) {
    }

    /**
     * Get all manufacturers.
     *
     * @return ManufacturerDTO[]
     */
    public function getAll(): array
    {
        return $this->manufacturerRepository->all();
    }

    /**
     * Create a new manufacturer.
     *
     * @param array<string, mixed> $requestValidated
     *
     * @return ManufacturerDTO
     */
    public function createManufacturer(array $requestValidated): ManufacturerDTO
    {
        $manufacturer = $this->manufacturerRepository->create([
            'name' => $requestValidated['name'],
        ]);

        $this->cacheManufacturers();

        return $manufacturer;
    }

    /**
     * Update an existing manufacturer by ID.
     *
     * @param int $id
     * @param array<string, mixed> $requestValidated
     *
     * @return ManufacturerDTO
     */
    public function updateManufacturer(int $id, array $requestValidated): ManufacturerDTO
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        $data = [
            'name' => $requestValidated['name'] ?? $manufacturer->name,
        ];

        $this->manufacturerRepository->update($id, $data);

        $this->cacheManufacturers();

        return $this->manufacturerRepository->find($id);
    }

    /**
     * Delete an existing manufacturer by ID.
     *
     * @param int $id
     *
     * @return bool|null
     */
    public function deleteManufacturer(int $id): ?bool
    {
        $isDeleted = $this->manufacturerRepository->delete($id);

        $this->cacheManufacturers();

        return $isDeleted;
    }

    /**
     * Cache manufacturers in storage.
     *
     * @return void
     */
    private function cacheManufacturers(): void
    {
        $manufacturers = $this->manufacturerRepository->all();

        $this->cache->put(self::CACHE_KEY, $manufacturers);
    }
}
