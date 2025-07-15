<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for storing a new manufacturer.
 */
readonly class ManufacturerStoreDTO
{
    public string $name;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
    }
}
