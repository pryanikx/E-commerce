<?php

namespace App\DTO\Service;

use App\Models\Service;

readonly class ServiceListDTO
{
    public array $data;

    public function __construct(Service $service) {
        $this->data = [
            'name' => $service->name,
            'description' => $service->description,
        ];
    }

    public function toArray(): array {
        return $this->data;
    }
}
