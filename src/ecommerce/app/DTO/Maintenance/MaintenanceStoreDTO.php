<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

/**
 * Data transfer object for storing a new maintenance.
 */
readonly class MaintenanceStoreDTO
{
    /**
     * @param string $name
     * @param string|null $description
     * @param string|null $duration
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $duration = null,
    ) {}
}
