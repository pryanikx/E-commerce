<?php

declare(strict_types=1);

namespace App\DTO\Manufacturer;

use App\Models\Manufacturer;

/**
 * Data transfer object for listing manufacturers.
 */
class ManufacturerListDTO
{
    /**
     * @var int $id
     */
    public int $id;

    /**
     * @var string $name
     */
    public string $name;

    /**
     * @param Manufacturer $manufacturer
     */
    public function __construct(Manufacturer $manufacturer)
    {
        $this->id = $manufacturer->id;
        $this->name = $manufacturer->name;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            ];
    }
}
