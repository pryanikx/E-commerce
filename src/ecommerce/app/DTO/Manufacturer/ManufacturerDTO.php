<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

class ManufacturerDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $created_at
     * @param string $updated_at
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $created_at,
        public string $updated_at,
    ) {
    }
}
