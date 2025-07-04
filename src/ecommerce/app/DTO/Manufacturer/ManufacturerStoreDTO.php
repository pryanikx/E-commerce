<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

/**
 * Data transfer object for storing a new manufacturer.
 */
readonly class ManufacturerStoreDTO
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
    }
}
