<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacturer\ManufacturerStoreRequest;
use App\Http\Requests\Manufacturer\ManufacturerUpdateRequest;
use App\Services\ManufacturerService;
use Illuminate\Http\JsonResponse;

class AdminManufacturerController extends Controller
{
    /**
     * @param ManufacturerService $manufacturerService
     */
    public function __construct(protected ManufacturerService $manufacturerService)
    {
    }

    /**
     * list all existing manufacturers.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $manufacturers = $this->manufacturerService->getAll();

        if (empty($manufacturers)) {
            return response()->json(['message' => __('messages.empty_manufacturers')], 200);
        }

        return response()->json($manufacturers, 200);
    }

    /**
     * store a new manufacturer.
     *
     * @param ManufacturerStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(ManufacturerStoreRequest $request): JsonResponse
    {
        $manufacturer = $this->manufacturerService->createManufacturer($request->validated());

        return response()->json($manufacturer, 201);
    }

    /**
     * update an existing manufacturer.
     *
     * @param int $id
     * @param ManufacturerUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(int $id, ManufacturerUpdateRequest $request): JsonResponse
    {
        $manufacturer = $this->manufacturerService->updateManufacturer($id, $request->validated());

        return response()->json($manufacturer, 200);
    }

    /**
     * erase an existing manufacturer.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->manufacturerService->deleteManufacturer($id);

        return response()->json(['message' => __('messages.deleted')], 200);
    }
}
