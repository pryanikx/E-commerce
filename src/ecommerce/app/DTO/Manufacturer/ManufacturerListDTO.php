<?php

namespace App\DTO\Manufacturer;

use App\Models\Manufacturer;

class ManufacturerListDTO
{
    public array $data;

    public function __construct(Manufacturer $product) {
        $this->data = [
            'name' => $product->name,
        ];
    }

    public function toArray(): array {
        return $this->data;
    }
}
