<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

class ManufacturerUpdateDTO
{
    /**
     * @param int $id
     * @param string|null $name
     */
    public function __construct(
        public int $id,
        public ?string $name,
    ) {
    }
}
