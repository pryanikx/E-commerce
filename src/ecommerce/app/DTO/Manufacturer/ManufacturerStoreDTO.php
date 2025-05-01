<?php

namespace App\DTO\Manufacturer;

use App\Http\Requests\ManufacturerStoreRequest;

readonly class ManufacturerStoreDTO
{
    public string $name;

    public function __construct(ManufacturerStoreRequest $request) {
        $this->name = $request->input('name');
    }
}
