<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

class MaintenanceStoreDTO
{
    /**
     * @param string $name
     * @param string|null $description
     * @param string|null $duration
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $duration,
    ) {
    }

    /**
     * Transform a DTO object to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
        ];
    }
}
