<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\MaintenanceStoreRequest;
use App\DTO\Maintenance\MaintenanceListDTO;
use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\Services\MaintenanceService;
use \Illuminate\Http\JsonResponse;

class MaintenanceController extends Controller
{
    public function __construct(protected MaintenanceService $maintenanceService) {}

    public function index() {
        $maintenances = $this->maintenanceService->getAll();

        if (!$maintenances) {
            return response()->json("No services found!", 404);
        }

        $result = $maintenances->map(fn($maintenance) => (new MaintenanceListDTO($maintenance))->toArray());

        return response()->json($result);

    }

    public  function store(MaintenanceStoreRequest $request)
    {
        $dto = new MaintenanceStoreDTO($request->validated());
        $maintenance = $this->maintenanceService->createMaintenance($dto);

        return response()->json($maintenance, 201);
    }

    public function updateMaintenance(int $id, MaintenanceStoreRequest $request): JsonResponse
    {
        $dto = new MaintenanceStoreDTO($request->validated());
        $maintenance = $this->maintenanceService->updateMaintenance($id, $dto);

        return response()->json($maintenance, 200);
    }

    public function deleteMaintenance(int $id) {
        $this->maintenanceService->deleteMaintenance($id);

        return response()->json("Successfully deleted!", 204);
    }
}
