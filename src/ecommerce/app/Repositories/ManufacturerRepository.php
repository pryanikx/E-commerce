<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * Get all manufacturers from the database.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Manufacturer::all();
    }

    /**
     * Find a manufacturer by ID.
     *
     * @param int $id
     *
     * @return Manufacturer
     */
    public function find(int $id): Manufacturer
    {
        return Manufacturer::findOrFail($id);
    }

    /**
     * Create a new manufacturer.
     *
     * @param array $array
     *
     * @return Manufacturer
     */
    public function create(array $array): Manufacturer
    {
        return Manufacturer::create($array);
    }

    /**
     * Update an existing manufacturer.
     *
     * @param Manufacturer $manufacturer
     * @param array $data
     *
     * @return bool
     */
    public function update(Manufacturer $manufacturer, array $data): bool
    {
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
}
