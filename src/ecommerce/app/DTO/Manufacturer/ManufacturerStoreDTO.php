<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

class ManufacturerStoreDTO
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name,
    ) {
    }
}
