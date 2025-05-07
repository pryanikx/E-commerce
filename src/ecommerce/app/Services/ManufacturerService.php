<?php

namespace App\Services;

use App\DTO\Manufacturer\ManufacturerListDTO;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerUpdateDTO;
use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManufacturerService
{
    public function __construct(protected ManufacturerRepositoryInterface $manufacturerRepository) {}

    public function getAll(): ?array
    {
        $manufacturers = $this->manufacturerRepository->all();

        return $manufacturers->map(fn($manufacturer)
            => (new ManufacturerListDTO($manufacturer))->toArray())->toArray();
    }

    public function createManufacturer(array $request_validated): Manufacturer
    {
        $dto = new ManufacturerStoreDTO($request_validated);

        return $this->manufacturerRepository->create([
            'name' => $dto->name,
        ]);
    }

    public function updateManufacturer(int $id, array $request_validated): Manufacturer
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        $dto = new ManufacturerUpdateDTO($request_validated);

        $data = [
            'name' => $dto->name !== null ? $dto->name : $manufacturer->name,
        ];

        $this->manufacturerRepository->update($manufacturer, $data);

        return $manufacturer->refresh();
    }

    public function deleteManufacturer(int $id): ?bool
    {
        return $this->manufacturerRepository->delete($id);
    }

}
