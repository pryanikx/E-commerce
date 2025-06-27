<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishCatalogJob;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ProductExportController extends Controller
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private readonly LoggerInterface $logger
    )
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
            PublishCatalogJob::dispatch();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.catalog_dispatched'),
            ], 200);
        } catch (\Exception $e) {
            $this->logger->error(__('errors.catalog_dispatch_failed'), ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => __('errors.catalog_dispatch_failed') . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadLatestCatalog()
    {
        // Example for S3/localstack
        $disk = Storage::disk('local'); // or 'local' if you use local storage
        $files = $disk->files('/'); // or the correct directory
        $csvFiles = array_filter($files, fn($f) => str_ends_with($f, '.csv'));

        if (empty($csvFiles)) {
            return response()->json(['error' => 'No catalog file found'], 404);
        }

        // Get the latest file by name (assuming timestamp in name)
        rsort($csvFiles);
        $latest = $csvFiles[0];

        $stream = $disk->readStream($latest);

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
        }, basename($latest), [
            'Content-Type' => 'text/csv',
        ]);
    }
}
