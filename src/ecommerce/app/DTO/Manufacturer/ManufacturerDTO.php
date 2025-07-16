<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

class ManufacturerDTO
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
}
