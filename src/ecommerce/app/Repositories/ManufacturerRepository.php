<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Manufacturer\ManufacturerDTO;
use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;

class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * Get all manufacturers from the database.
     *
     * @return ManufacturerDTO[]
     */
    public function all(): array
    {
        return Manufacturer::all()->map(fn (Manufacturer $manufacturer)
            => $this->mapToDTO($manufacturer))->all();
    }

    /**
     * Find a manufacturer by ID.
     *
     * @param int $id
     *
     * @return ManufacturerDTO
     */
    public function find(int $id): ManufacturerDTO
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return $this->mapToDTO($manufacturer);
    }

    /**
     * Create a new manufacturer.
     *
     * @param array<string, mixed> $array
     *
     * @return ManufacturerDTO
     */
    public function create(array $array): ManufacturerDTO
    {
        $manufacturer = Manufacturer::create($array);

        return $this->mapToDTO($manufacturer);
    }

    /**
     * Update an existing manufacturer.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return $manufacturer->update($data);
    }

    /**
     * Delete a manufacturer by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        return (bool) Manufacturer::destroy($id);
    }

    /**
     * Map Eloquent model to DTO.
     *
     * @param Manufacturer $manufacturer
     *
     * @return ManufacturerDTO
     */
    private function mapToDTO(Manufacturer $manufacturer): ManufacturerDTO
    {
        return new ManufacturerDTO(
            $manufacturer->id,
            $manufacturer->name,
        );
    }
}
