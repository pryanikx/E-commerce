<?php

namespace App\DTO\Manufacturer;

class ManufacturerUpdateDTO
{
    public ?string $name;

    public function __construct(array $request_data) {
        $this->name = $request_data['name'] ?? null;
    }
}
