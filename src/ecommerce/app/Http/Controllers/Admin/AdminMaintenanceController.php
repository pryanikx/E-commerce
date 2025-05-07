<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Maintenance\MaintenanceStoreRequest;
use App\Http\Requests\Maintenance\MaintenanceUpdateRequest;
use App\Services\MaintenanceService;
use Illuminate\Http\JsonResponse;

class AdminMaintenanceController extends Controller
{
    public function __construct(protected MaintenanceService $maintenanceService) {}

    public function index(): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAll();

        if (empty($maintenances)) {
            return response()->json(['message' => 'No services found!'], 200);
        }

        return response()->json($maintenances, 200);
    }

    public function store(MaintenanceStoreRequest $request): JsonResponse
    {
        $maintenance = $this->maintenanceService->createMaintenance($request->validated());

        return response()->json($maintenance, 201);
    }

    public function update(int $id, MaintenanceUpdateRequest $request): JsonResponse
    {
        $maintenance = $this->maintenanceService->updateMaintenance($id, $request->validated());

        return response()->json($maintenance, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->maintenanceService->deleteMaintenance($id);

        return response()->json(['message' => 'Successfully deleted!'], 200);
    }
}
