<?php

namespace App\DTO\Manufacturer;

use App\Models\Manufacturer;

class ManufacturerListDTO
{
    public int $id;
    public string $name;

    public function __construct(Manufacturer $manufacturer) {
        $this->id = $manufacturer->id;
        $this->name = $manufacturer->name;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            ];
    }
}
