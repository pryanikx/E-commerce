<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

readonly class ManufacturerStoreDTO
{
    public string $name;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
    }
}
