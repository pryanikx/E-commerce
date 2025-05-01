<?php

namespace App\DTO\Manufacturer;

readonly class ManufacturerStoreDTO
{
    public string $name;

    public function __construct(array $request_data) {
        $this->name = $request_data['name'];
    }
}
