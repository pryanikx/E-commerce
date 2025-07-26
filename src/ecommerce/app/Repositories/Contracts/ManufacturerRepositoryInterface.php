<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Manufacturer\ManufacturerDTO;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerUpdateDTO;

interface ManufacturerRepositoryInterface
{
    /**
     * Get all manufacturers from the database.
     *
     * @return ManufacturerDTO[]
     */
    public function all(): array;

    /**
     * Find a manufacturer by ID.
     *
     * @param int $id
     *
     * @return ManufacturerDTO
     */
    public function find(int $id): ManufacturerDTO;

    /**
     * Create a new manufacturer.
     *
     * @param ManufacturerStoreDTO $dto
     *
     * @return ManufacturerDTO
     */
    public function create(ManufacturerStoreDTO $dto): ManufacturerDTO;

    /**
     * Update an existing manufacturer.
     *
     * @param ManufacturerUpdateDTO $dto
     *
     * @return bool
     */
    public function update(ManufacturerUpdateDTO $dto): bool;

    /**
     * Delete a manufacturer by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
