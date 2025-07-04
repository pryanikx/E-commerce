<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

use App\Models\Maintenance;

/**
 * Data transfer object for listing maintenances.
 */
readonly class MaintenanceListDTO
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
     * @var string|null $description
     */
    public ?string $description;

    /**
     * @var string|null $duration
     */
    public ?string $duration;

    /**
     * @param Maintenance $maintenance
     */
    public function __construct(Maintenance $maintenance)
    {
        $this->id = $maintenance->id;
        $this->name = $maintenance->name;
        $this->description = $maintenance->description;
        $this->duration = $maintenance->duration;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
        ];
    }
}
