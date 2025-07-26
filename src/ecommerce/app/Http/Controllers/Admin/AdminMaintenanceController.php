<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Maintenance\MaintenanceStoreDTO;
use App\DTO\Maintenance\MaintenanceUpdateDTO;
use App\Exceptions\DeleteDataException;
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
    public function __construct(
        private readonly MaintenanceService $maintenanceService
    ) {
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

        return response()->json(
            [
            'data' => $maintenances,
            ],
            200
        );
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
        $requestValidated = $request->validated();

        $maintenance = $this->maintenanceService->createMaintenance(
            new MaintenanceStoreDTO(
                $requestValidated['name'],
                $requestValidated['description'],
                $requestValidated['duration'],
            )
        );

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
        $requestValidated = $request->validated();

        $maintenance = $this->maintenanceService->updateMaintenance(
            new MaintenanceUpdateDTO(
                $requestValidated['id'],
                $requestValidated['name'],
                $requestValidated['description'],
                $requestValidated['duration'],
            )
        );

        return response()->json($maintenance, 200);
    }

    /**
     * erase existing maintenance.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws DeleteDataException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->maintenanceService->deleteMaintenance($id);

        return response()->json(['message' => __('messages.deleted')], 200);
    }
}
