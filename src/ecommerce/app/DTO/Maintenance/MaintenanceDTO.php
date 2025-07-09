<?php

declare(strict_types=1);

namespace App\DTO\Maintenance;

class MaintenanceDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $duration,
        public string $created_at,
        public string $updated_at,
    ) {}
} 