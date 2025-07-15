<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

/**
 * Data transfer object for listing maintenances.
 */
readonly class MaintenanceListDTO
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
            'description' => $this->description,
            'duration' => $this->duration,
        ];
    }
}
