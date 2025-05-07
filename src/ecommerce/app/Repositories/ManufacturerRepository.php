<?php

namespace App\Repositories;

use App\Models\Manufacturer;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    public function all(): Collection
    {
        return Manufacturer::all();
    }

    public function find(int $id): Manufacturer
    {
        return Manufacturer::findOrFail($id);
    }

    public function update(Manufacturer $manufacturer, array $data): bool
    {
        return $manufacturer->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Manufacturer::destroy($id);
    }

    public function create(array $array): Manufacturer
    {
        return Manufacturer::create($array);
    }
}
