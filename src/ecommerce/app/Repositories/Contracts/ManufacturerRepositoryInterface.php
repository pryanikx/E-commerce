<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Manufacturer\ManufacturerDTO;

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
     * @param array<string, mixed> $array
     *
     * @return ManufacturerDTO
     */
    public function create(array $array): ManufacturerDTO;

    /**
     * Update an existing manufacturer.
     *
     * @param int $id
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a manufacturer by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
