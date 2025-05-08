<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

use App\Models\Maintenance;

readonly class MaintenanceListDTO
{
    public int $id;
    public string $name;
    public ?string $description;
    public ?string $duration;

    public function __construct(Maintenance $maintenance)
    {
        $this->id = $maintenance->id;
        $this->name = $maintenance->name;
        $this->description = $maintenance->description;
        $this->duration = $maintenance->duration;
    }

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
