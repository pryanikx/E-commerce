<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerListDTO;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerUpdateDTO;
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
        private ManufacturerRepositoryInterface $manufacturerRepository,
        private CacheInterface $cache,
    )
    {
    }

    /**
     * Get all manufacturers.
     *
     * @return array
     */
    public function getAll(): array
    {
        $manufacturers = $this->manufacturerRepository->all();
        return [
            'data' => array_map(fn($manufacturer) => $this->makeManufacturerListDTO($manufacturer)->toArray(), $manufacturers),
        ];
    }

    /**
     * Create a new manufacturer.
     *
     * @param array $request_validated
     *
     * @return ManufacturerDTO
     */
    public function createManufacturer(array $request_validated): ManufacturerDTO
    {
        $dto = new ManufacturerStoreDTO($request_validated);

        $manufacturer = $this->manufacturerRepository->create([
            'name' => $dto->name,
        ]);

        $this->cacheManufacturers();

        return $manufacturer;
    }

    /**
     * Update an existing manufacturer by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return ManufacturerDTO
     */
    public function updateManufacturer(int $id, array $request_validated): ManufacturerDTO
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        $dto = new ManufacturerUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name ?? $manufacturer->name,
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
        $is_deleted = $this->manufacturerRepository->delete($id);

        $this->cacheManufacturers();

        return $is_deleted;
    }

    /**
     * Cache manufacturers in storage.
     *
     * @return void
     */
    private function cacheManufacturers(): void
    {
        $manufacturers = $this->manufacturerRepository->all();
        $data = array_map(fn($manufacturer) => $this->makeManufacturerListDTO($manufacturer)->toArray(), $manufacturers);
        $this->cache->put(self::CACHE_KEY, $data);
    }

    /**
     * Make DTO for manufacturers.
     *
     * @param mixed $manufacturer
     *
     * @return ManufacturerListDTO
     */
    private function makeManufacturerListDTO($manufacturer): ManufacturerListDTO
    {
        if ($manufacturer instanceof ManufacturerListDTO) {
            return $manufacturer;
        }
        if ($manufacturer instanceof ManufacturerDTO) {
            return new ManufacturerListDTO($manufacturer->id, $manufacturer->name);
        }
        return new ManufacturerListDTO($manufacturer['id'] ?? $manufacturer->id, $manufacturer['name'] ?? $manufacturer->name);
    }
}
