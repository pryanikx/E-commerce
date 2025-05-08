<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

readonly class MaintenanceStoreDTO
{
    public string $name;
    public ?string $description;
    public ?string $duration;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->description = $request_data['description'] ?? null;
        $this->duration = $request_data['duration'] ?? null;
    }
}
