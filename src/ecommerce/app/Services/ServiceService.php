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

        return $this->serviceRepository->create([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
    }

    public function updateService(int $id, ServiceStoreDTO $dto): bool
    {
        $service = $this->serviceRepository->find($id);

        return $this->serviceRepository->update($service, [
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
    }

    public function deleteService(int $id): ?bool
    {
        return $this->serviceRepository->delete($id);
    }
}
