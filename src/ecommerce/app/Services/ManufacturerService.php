<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerListDTO;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerUpdateDTO;
use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;

class ManufacturerService
{
    private const CACHE_KEY = 'manufacturers';

    /**
     * @param ManufacturerRepositoryInterface $manufacturerRepository
     */
    public function __construct(protected ManufacturerRepositoryInterface $manufacturerRepository)
    {
    }

    /**
     * Get all manufacturers.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        return cache()->rememberForever(self::CACHE_KEY, function () {
            $manufacturers = $this->manufacturerRepository->all();

            return $manufacturers->map(fn($manufacturer) => (new ManufacturerListDTO($manufacturer))->toArray())->toArray();
        });
    }

    /**
     * Create a new manufacturer.
     *
     * @param array $request_validated
     *
     * @return Manufacturer
     */
    public function createManufacturer(array $request_validated): Manufacturer
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
     * @return Manufacturer
     */
    public function updateManufacturer(int $id, array $request_validated): Manufacturer
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        $dto = new ManufacturerUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name !== null ? $dto->name : $manufacturer->name,
        ];

        $this->manufacturerRepository->update($manufacturer, $data);

        $this->cacheManufacturers();

        return $manufacturer->refresh();
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
     * Save manufacturers in cache
     *
     * @return void
     */
    private function cacheManufacturers(): void
    {
        $manufacturers = $this->manufacturerRepository->all();
        $data = $manufacturers->map(fn($manufacturer) =>
        (new ManufacturerListDTO($manufacturer))->toArray())->toArray();

        cache()->put(self::CACHE_KEY, $data);
    }
}
