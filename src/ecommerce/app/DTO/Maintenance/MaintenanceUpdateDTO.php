<?php

namespace App\DTO\Maintenance;

class MaintenanceUpdateDTO
{
    public ?string $name;
    public ?string $description;
    public ?string $duration;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'] ?? null;
        $this->description = $request_data['description'] ?? null;
        $this->duration = $request_data['duration'] ?? null;
    }
}
