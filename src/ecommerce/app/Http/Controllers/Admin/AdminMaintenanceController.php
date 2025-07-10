<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Maintenance\MaintenanceStoreRequest;
use App\Http\Requests\Maintenance\MaintenanceUpdateRequest;
use App\Services\MaintenanceService;
use Illuminate\Http\JsonResponse;

class AdminMaintenanceController extends Controller
{
    /**
     * @param MaintenanceService $maintenanceService
     */
    public function __construct(private readonly MaintenanceService $maintenanceService)
    {
    }

    /**
     * list all existing maintenances.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAll();

        if (empty($maintenances)) {
            return response()->json(['message' => __('messages.empty_maintenances')], 200);
        }

        return response()->json($maintenances, 200);
    }

    /**
     * store new maintenance.
     *
     * @param MaintenanceStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(MaintenanceStoreRequest $request): JsonResponse
    {
        $maintenance = $this->maintenanceService->createMaintenance($request->validated());

        return response()->json($maintenance, 201);
    }

    /**
     * update existing maintenance.
     *
     * @param int $id
     * @param MaintenanceUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(int $id, MaintenanceUpdateRequest $request): JsonResponse
    {
        $maintenance = $this->maintenanceService->updateMaintenance($id, $request->validated());

        return response()->json($maintenance, 200);
    }

    /**
     * erase existing maintenance.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if ($this->maintenanceService->deleteMaintenance($id)) {
            return response()->json(['message' => __('messages.deleted')], 200);
        }

        return response()->json(['message' => __('messages.empty_maintenances')], 200);
    }
}
