<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacturer\ManufacturerStoreRequest;
use App\Http\Requests\Manufacturer\ManufacturerUpdateRequest;
use App\Services\ManufacturerService;
use Illuminate\Http\JsonResponse;

class AdminManufacturerController extends Controller
{
    public function __construct(protected ManufacturerService $manufacturerService) {}

    public function index() : JsonResponse
    {
        $manufacturers = $this->manufacturerService->getAll();

        if (empty($manufacturers)) {
            return response()->json(['message' => 'No manufacturers found!'], 200);
        }

        return response()->json($manufacturers, 200);
    }

    public function store(ManufacturerStoreRequest $request) : JsonResponse
    {
        $manufacturer = $this->manufacturerService->createManufacturer($request->validated());

        return response()->json($manufacturer, 201);
    }

    public function update(int $id, ManufacturerUpdateRequest $request): JsonResponse
    {
        $manufacturer = $this->manufacturerService->updateManufacturer($id, $request->validated());

        return response()->json($manufacturer, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->manufacturerService->deleteManufacturer($id);

        return response()->json(['message' => 'Successfully deleted!'], 200);
    }
}
