<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

/**
 * Data transfer object for updating a maintenance.
 */
readonly class MaintenanceUpdateDTO
{
    /**
     * @param string|null $name
     * @param string|null $description
     * @param string|null $duration
     */
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?string $duration = null,
    ) {}
}
