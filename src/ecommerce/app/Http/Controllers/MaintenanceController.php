<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceStoreRequest;
use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\Services\MaintenanceService;
use Illuminate\Http\JsonResponse;

class MaintenanceController extends Controller
{
    public function __construct(protected MaintenanceService $maintenanceService) {}

    public function index(): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAll();

        if ($maintenances->isEmpty()) {
            return response()->json(['message' => 'No services found!'], 200);
        }

        $result = $maintenances->map(fn($maintenance) => (new MaintenanceListDTO($maintenance))->toArray());

        return response()->json($result);
    }

    public function store(MaintenanceStoreRequest $request): JsonResponse
    {
        $dto = new MaintenanceStoreDTO($request->validated());
        $maintenance = $this->maintenanceService->createMaintenance($dto);

        return response()->json($maintenance, 201);
    }

    public function update(int $id, MaintenanceStoreRequest $request): JsonResponse
    {
        $dto = new MaintenanceStoreDTO($request->validated());
        $maintenance = $this->maintenanceService->updateMaintenance($id, $dto);

        return response()->json($maintenance, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->maintenanceService->deleteMaintenance($id);

        return response()->json(['message' => 'Successfully deleted!'], 204);
    }
}
