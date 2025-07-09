<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExportCatalogJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminProductExportController extends Controller
{
    private const EXPORT_ID_PREFIX = 'catalog_export_';
    private const QUEUE_NAME = 'catalog_export';
    private const STATUS_QUEUED = 'queued';

    /**
     * Export product catalog to CSV and send to RabbitMQ queue
     *
     * @return JsonResponse
     */
    public function exportCatalog(): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser();

            $exportId = $this->generateExportId();

            $this->dispatchExportJob($exportId, $user->email);

            return $this->successResponse([
                'message' => __('messages.catalog_export_started'),
                'export_id' => $exportId,
                'status' => self::STATUS_QUEUED
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse(
                __('messages.catalog_export_start_failed'),
                $e->getMessage()
            );
        }
    }

    /**
     * Get authenticated user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Generate unique export ID
     *
     * @return string
     */
    private function generateExportId(): string
    {
        return uniqid(self::EXPORT_ID_PREFIX, true);
    }

    /**
     * Dispatch export job to queue
     *
     * @param string $exportId
     * @param string $userEmail
     */
    private function dispatchExportJob(string $exportId, string $userEmail): void
    {
        ExportCatalogJob::dispatch($exportId, $userEmail)
            ->onQueue(self::QUEUE_NAME);
    }

    /**
     * Return success response
     *
     * @param array $data
     * @return JsonResponse
     */
    private function successResponse(array $data): JsonResponse
    {
        return response()->json($data, 200);
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param string $error
     * @return JsonResponse
     */
    private function errorResponse(string $message, string $error): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => $error
        ], 500);
    }
}
