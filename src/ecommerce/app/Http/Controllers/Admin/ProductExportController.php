<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishCatalogJob;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductExportController extends Controller
{
    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * Export CSV product catalog
     *
     * @return JsonResponse
     */
    public function export(): JsonResponse
    {
        try {
            PublishCatalogJob::dispatch($this->productRepository);
            return response()->json([
                'status' => 'success',
                'message' => __('messages.catalog_dispatched'),
            ], 200);
        } catch (\Exception $e) {
            Log::error(__('errors.catalog_dispatch_failed'), ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => __('errors.catalog_dispatch_failed') . $e->getMessage(),
            ], 500);
        }
    }
}
