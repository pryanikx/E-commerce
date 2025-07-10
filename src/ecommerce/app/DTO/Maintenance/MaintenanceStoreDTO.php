<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

/**
 * Data transfer object for storing a new maintenance.
 */
readonly class MaintenanceStoreDTO
{
    public string $name;
    public ?string $description;
    public ?string $duration;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->duration = $data['duration'] ?? null;
    }
}
