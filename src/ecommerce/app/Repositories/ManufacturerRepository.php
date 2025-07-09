<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Manufacturer\ManufacturerDTO;
use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * @return ManufacturerDTO[]
     */
    public function all(): array
    {
        return Manufacturer::all()->map(fn(Manufacturer $manufacturer) => $this->mapToDTO($manufacturer))->all();
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
     * @param array $array
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
     * @param array $data
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
     * @param Manufacturer $manufacturer
     * @return ManufacturerDTO
     */
    private function mapToDTO(Manufacturer $manufacturer): ManufacturerDTO
    {
        return new ManufacturerDTO(
            $manufacturer->id,
            $manufacturer->name,
            $manufacturer->created_at?->toISOString() ?? '',
            $manufacturer->updated_at?->toISOString() ?? '',
        );
    }
}
