<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\ServiceStoreRequest;
use App\DTO\Service\ServiceListDTO;
use App\DTO\Service\ServiceStoreDTO;
use App\Services\ServiceService;


class ServiceController extends Controller
{
    public function __construct(protected ServiceService $serviceService) {}

    public function index() {
        $services = $this->serviceService->getAll();

        if (!$services) {
            return response()->json("No services found!", 404);
        }

        $result = $services->map(fn($service) => (new ServiceListDTO($service))->toArray());

        return response()->json($result);

    }

    public  function store(ServiceStoreRequest $request)
    {
        $dto = new ServiceStoreDTO($request);
        $service = $this->serviceService->createService($dto);

        return response()->json($service, 201);
    }

    public function deleteService(int $id) {
        $this->serviceService->deleteService($id);

        return response()->json("Successfully deleted!", 204);
    }
}
