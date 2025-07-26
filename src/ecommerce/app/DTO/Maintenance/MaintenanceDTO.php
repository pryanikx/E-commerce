<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

class MaintenanceDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string|null $description
     * @param string|null $duration
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $duration,
    ) {
    }
}
