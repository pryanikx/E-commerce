<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

/**
 * Data transfer object for updating a maintenance.
 */
readonly class MaintenanceUpdateDTO
{
    public ?string $name;
    public ?string $description;
    public ?string $duration;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->duration = $data['duration'] ?? null;
    }
}
