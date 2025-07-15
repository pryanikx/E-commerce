<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for updating a manufacturer.
 */
readonly class ManufacturerUpdateDTO
{
    public ?string $name;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
    }
}
