<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Collection;

interface ManufacturerRepositoryInterface
{
    /**
     * Get all manufacturers from the database.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a manufacturer by ID.
     *
     * @param int $id
     *
     * @return Manufacturer
     */
    public function find(int $id): Manufacturer;

    /**
     * Create a new manufacturer.
     *
     * @param array $array
     *
     * @return Manufacturer
     */
    public function create(array $array): Manufacturer;

    /**
     * Update an existing manufacturer.
     *
     * @param Manufacturer $manufacturer
     * @param array $data
     *
     * @return bool
     */
    public function update(Manufacturer $manufacturer, array $data): bool;

    /**
     * Delete a manufacturer by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
