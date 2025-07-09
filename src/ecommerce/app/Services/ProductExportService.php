<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Controllers\User\ProductController;
use App\Models\Product;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use App\Services\Support\CsvWriterFactoryInterface;

class ProductExportService
{
    private const EXPORT_DIRECTORY = 'app/exports';
    private const FILE_PREFIX = 'catalog_export_';
    private const FILE_EXTENSION = '.csv';
    private const DIRECTORY_PERMISSIONS = 0755;
    private const STARTING_PAGE = 1;

    private const CSV_HEADERS = [
        'ID',
        'Name',
        'Article',
        'Manufacturer_Name',
        'Price_USD',
        'Price_EUR',
        'Price_RUB',
        'Image_URL',
    ];

    public function __construct(
        private ProductController $productController,
        private LoggerInterface $logger,
        private CsvWriterFactoryInterface $csvWriterFactory,
    ) {
    }

    /**
     * Export all products to CSV file
     *
     * @param string $exportId
     * @return string Path to the created CSV file
     * @throws \Exception
     */
    public function exportToCSV(string $exportId): string
    {
        try {
            $filePath = $this->createExportFile($exportId);
            $csv = $this->initializeCsvWriter($filePath);

            $totalExported = $this->exportProductsToCSV($csv, $exportId);

            $this->logger->info(__('messages.catalog_export_completed'), [
                'export_id' => $exportId,
                'total_products' => $totalExported,
                'file_path' => $filePath
            ]);

            return $filePath;

        } catch (\Exception $e) {
            $this->logger->error(__('errors.catalog_export_failed'), [
                'export_id' => $exportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Get export statistics
     *
     * @return array
     */
    public function getExportStats(): array
    {
        return [
            'total_products' => Product::count(),
            'products_with_images' => Product::whereNotNull('image_path')->count(),
            'products_with_manufacturer' => Product::whereHas('manufacturer')->count(),
            'products_with_category' => Product::whereHas('category')->count(),
        ];
    }

    /**
     * Create export file path and ensure directory exists
     *
     * @param string $exportId
     * @return string
     */
    private function createExportFile(string $exportId): string
    {
        $filename = self::FILE_PREFIX . $exportId . self::FILE_EXTENSION;
        $filePath = storage_path(self::EXPORT_DIRECTORY . '/' . $filename);

        $this->ensureDirectoryExists(dirname($filePath));

        return $filePath;
    }

    /**
     * Ensure directory exists
     *
     * @param string $directory
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, self::DIRECTORY_PERMISSIONS, true);
        }
    }

    /**
     * Initialize CSV writer with headers
     *
     * @param string $filePath
     *
     * @return Writer
     */
    private function initializeCsvWriter(string $filePath): \League\Csv\Writer
    {
        $csv = $this->csvWriterFactory->createFromPath($filePath, 'w+');
        $csv->setOutputBOM(\League\Csv\Writer::BOM_UTF8);
        $csv->insertOne(self::CSV_HEADERS);

        return $csv;
    }

    /**
     * Export products to CSV using pagination
     *
     * @param Writer $csv
     * @param string $exportId
     * @return int Total number of exported products
     */
    private function exportProductsToCSV(Writer $csv, string $exportId): int
    {
        $currentPage = self::STARTING_PAGE;
        $totalExported = 0;

        do {
            $response = $this->productController->index(['page' => $currentPage]);
            $responseData = $response->getData(true);

            $totalExported += $this->processProductsPage($csv, $responseData['data']);

            $currentPage++;
        } while ($currentPage <= $responseData['meta']['last_page']);

        return $totalExported;
    }

    /**
     * Process a page of products and add to CSV
     *
     * @param Writer $csv
     * @param array $products
     * @return int Number of products processed
     */
    private function processProductsPage(Writer $csv, array $products): int
    {
        $processed = 0;

        foreach ($products as $productData) {
            $row = $this->prepareProductRowFromAPI($productData);
            $csv->insertOne($row);
            $processed++;
        }

        return $processed;
    }

    /**
     * Prepare product row data from API response
     *
     * @param array $productData
     * @return array
     */
    private function prepareProductRowFromAPI(array $productData): array
    {
        return [
            $productData['id'],
            $productData['name'],
            $productData['article'],
            $productData['manufacturer_name'],
            $productData['prices']['USD'] ?? 0,
            $productData['prices']['EUR'] ?? 0,
            $productData['prices']['RUB'] ?? 0,
            $productData['image_url'],
        ];
    }
}
