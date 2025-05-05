<?php

namespace App\DTO\Manufacturer;

use App\Models\Manufacturer;

class ManufacturerListDTO
{

    public string $name;

    public function __construct(Manufacturer $product) {
        $this->name = $product->name;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            ];
    }
}
