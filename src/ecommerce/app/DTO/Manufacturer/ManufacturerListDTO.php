<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for listing manufacturers.
 */
readonly class ManufacturerListDTO
{
    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
