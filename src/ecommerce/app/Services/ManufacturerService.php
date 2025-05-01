<?php

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManufacturerService
{
    public function __construct(protected ManufacturerRepositoryInterface $manufacturerRepository) {}

    public function getAll(): ?Collection  {
        return $this->manufacturerRepository->all();
    }

    public function createManufacturer(ManufacturerStoreDTO $dto): Manufacturer {

        return $this->manufacturerRepository->create([
            'name' => $dto->name,
        ]);
    }

    public function updateManufacturer(int $id, ManufacturerStoreDTO $dto): bool {
        $manufacturer = $this->getManufacturer($id);

        return $this->manufacturerRepository->update($manufacturer, [
            'name' => $dto->name,
        ]);
    }

    public function getManufacturer(int $id): ?Manufacturer {
        return $this->manufacturerRepository->find($id);
    }

    public function deleteManufacturer(int $id): ?bool
    {
        return $this->manufacturerRepository->delete($id);
    }

}
