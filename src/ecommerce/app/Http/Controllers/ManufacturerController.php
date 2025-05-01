<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufacturerStoreRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ManufacturerService;
use App\DTO\Manufacturer\ManufacturerStoreDTO;
use App\DTO\Manufacturer\ManufacturerListDTO;
use \Illuminate\Http\JsonResponse;

class ManufacturerController extends Controller
{
    public function __construct(protected ManufacturerService $manufacturerService) {}

    public function index() : JsonResponse
    {
        $manufacturers = $this->manufacturerService->getAll();

        if (!$manufacturers) {
            return response()->json("No services found!", 404);
        }

        $result = $manufacturers->map(fn($manufacturer) => (new ManufacturerListDTO($manufacturer))->toArray());

        return response()->json($result);
    }

    public function store(ManufacturerStoreRequest $request) : JsonResponse
    {
        $dto = new ManufacturerStoreDTO($request->validated());

        $manufacturer = $this->manufacturerService->createManufacturer($dto);

        return response()->json($manufacturer, 201);
    }

    public function updateManufacturer(int $id, ManufacturerStoreRequest $request): JsonResponse
    {
        $dto = new ManufacturerStoreDTO($request->validated());
        $product = $this->manufacturerService->updateManufacturer($id, $dto);

        return response()->json($product, 200);
    }

    public function deleteManufacturer(int $id): JsonResponse
    {
        $this->manufacturerService->deleteManufacturer($id);

        return response()->json("Successfully deleted!", 204);
    }
}
