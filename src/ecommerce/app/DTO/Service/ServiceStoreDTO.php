<?php

namespace App\DTO\Service;

use App\Http\Requests\ServiceStoreRequest;

readonly class ServiceStoreDTO
{
    public string $name;
    public string $description;

    public function __construct(ServiceStoreRequest $request) {
        $this->name = $request->input('name');
        $this->description = $request->input('description');
    }
}
