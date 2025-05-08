<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

class ManufacturerUpdateDTO
{
    /**
     * @var string|null $name
     */
    public ?string $name;

    /**
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'] ?? null;
    }
}
