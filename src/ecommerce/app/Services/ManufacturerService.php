<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerDTO;
use App\DTO\Manufacturer\ManufacturerListDTO;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerUpdateDTO;
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
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $manufacturers = $this->manufacturerRepository->all();
        return array_map(
            fn ($manufacturer) =>
            $this->makeManufacturerListDTO($manufacturer)->toArray(),
            $manufacturers
        );
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
        $dto = new ManufacturerStoreDTO($requestValidated);

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
     * @param array<string, mixed> $requestValidated
     *
     * @return ManufacturerDTO
     */
    public function updateManufacturer(int $id, array $requestValidated): ManufacturerDTO
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        $dto = new ManufacturerUpdateDTO($requestValidated);

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
        $data = array_map(fn ($manufacturer) => $this->makeManufacturerListDTO($manufacturer)->toArray(), $manufacturers);
        $this->cache->put(self::CACHE_KEY, $data);
    }

    /**
     * Make DTO for manufacturers.
     *
     * @param mixed $manufacturer
     *
     * @return ManufacturerListDTO
     */
    private function makeManufacturerListDTO(mixed $manufacturer): ManufacturerListDTO
    {
        if ($manufacturer instanceof ManufacturerListDTO) {
            return $manufacturer;
        }

        if ($manufacturer instanceof ManufacturerDTO) {
            return new ManufacturerListDTO($manufacturer->id, $manufacturer->name);
        }

        return new ManufacturerListDTO(
            $manufacturer['id'] ?? $manufacturer->id,
            $manufacturer['name'] ?? $manufacturer->name
        );
    }
}
