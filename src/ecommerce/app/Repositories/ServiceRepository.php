<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use \Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function all(): ?Collection
    {
        return Service::all();
    }

    public function find(int $id): ?Service
    {
        return Service::findOrFail($id);
    }

    public function update(Service $service, array $data): bool
    {
        return $service->update($data);
    }

    public function delete(int $id): ?bool
    {
        return Service::destroy($id);
    }

    public function create(array $array): Service
    {
        return Service::create($array);
    }
}
