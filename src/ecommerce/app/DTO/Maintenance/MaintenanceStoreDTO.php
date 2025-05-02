<?php

namespace App\DTO\Maintenance;

readonly class MaintenanceStoreDTO
{
    public string $name;
    public string $description;

    public function __construct($request_data) {
        $this->name = $request_data['name'];
        $this->description = $request_data['description'];
    }
}
