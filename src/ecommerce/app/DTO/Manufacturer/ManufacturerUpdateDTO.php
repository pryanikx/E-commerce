<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for updating a manufacturer.
 */
readonly class ManufacturerUpdateDTO
{
    /**
     * @param string|null $name
     */
    public function __construct(
        public ?string $name = null,
    ) {}
}
