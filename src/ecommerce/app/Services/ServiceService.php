<?php

namespace App\Services;

use App\DTO\Service\ServiceStoreDTO;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function __construct(protected ServiceRepositoryInterface $serviceRepository) {}

    public function getAll(): ?Collection  {
        return $this->serviceRepository->all();
    }

    public function createService(ServiceStoreDTO $dto): Service {

        $created_service = $this->serviceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);

        return $created_service;
    }

    public function deleteService(int $id): ?bool
    {
        return $this->serviceRepository->delete($id);
    }
}
