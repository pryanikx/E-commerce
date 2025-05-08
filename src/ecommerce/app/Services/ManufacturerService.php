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
        $manufacturers = $this->manufacturerRepository->all();

        return $manufacturers->map(fn ($manufacturer)
            => (new ManufacturerListDTO($manufacturer))->toArray())->toArray();
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

        return $this->manufacturerRepository->create([
            'name' => $dto->name,
        ]);
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
        return $this->manufacturerRepository->delete($id);
    }
}
