<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

readonly class MaintenanceStoreDTO
{
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
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->description = $request_data['description'] ?? null;
        $this->duration = $request_data['duration'] ?? null;
    }
}
