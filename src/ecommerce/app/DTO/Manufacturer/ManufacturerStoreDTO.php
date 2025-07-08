<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for storing a new manufacturer.
 */
readonly class ManufacturerStoreDTO
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name,
    ) {}
}
