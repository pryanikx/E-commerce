<?php

namespace App\DTO\Maintenance;

use App\Models\Maintenance;

readonly class MaintenanceListDTO
{
    public array $data;

    public function __construct(Maintenance $maintenance) {
        $this->data = [
            'name' => $maintenance->name,
            'description' => $maintenance->description,
        ];
    }

    public function toArray(): array {
        return $this->data;
    }
}
