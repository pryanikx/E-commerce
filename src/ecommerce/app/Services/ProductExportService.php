<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductStatsDTO;
use App\Services\Support\CsvWriterFactoryInterface;
use App\Services\Support\StorageServiceInterface;
use League\Csv\ByteSequence;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;

class ProductExportService
{
    private const CSV_HEADERS = [
        'ID',
        'Name',
        'Article',
        'Manufacturer_Name',
        'Price_USD',
        'Price_EUR',
        'Price_RUB',
        'Price_BYN',
        'Image_URL',
    ];

    /**
     * @param ProductService $productService
     * @param LoggerInterface $logger
     * @param CsvWriterFactoryInterface $csvWriterFactory
     * @param StorageServiceInterface $storageService
     * @param string $exportDirectory
     * @param string $filePrefix
     * @param string $fileExtension
     */
    public function __construct(
        private ProductService $productService,
        private LoggerInterface $logger,
        private CsvWriterFactoryInterface $csvWriterFactory,
        private StorageServiceInterface $storageService,
        private string $exportDirectory,
        private string $filePrefix,
        private string $fileExtension,
    ) {
    }

    /**
     * Export all products to CSV file
     *
     * @param string $exportId
     *
     * @return string
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
     * Get statistics for export.
     *
     * @return ProductStatsDTO
     */
    public function getExportStats(): ProductStatsDTO
    {
        return $this->productService->getStats();
    }

    /**
     * Create an export file path and ensure the directory exists
     *
     * @param string $exportId
     * @return string
     */
    private function createExportFile(string $exportId): string
    {
        $filename = $this->filePrefix . $exportId . $this->fileExtension;
        $filePath = $this->storageService->path($this->exportDirectory . '/' . $filename);
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
        if (!$this->storageService->exists($directory)) {
            $this->storageService->makeDirectory($directory);
        }
    }

    /**
     * Initialize CSV writer with headers
     *
     * @param string $filePath
     *
     * @return Writer
     */
    private function initializeCsvWriter(string $filePath): Writer
    {
        $csv = $this->csvWriterFactory->createFromPath($filePath, 'w+');
        $csv->setOutputBOM(ByteSequence::BOM_UTF8);
        $csv->insertOne(self::CSV_HEADERS);

        return $csv;
    }

    /**
     * Export all products to CSV
     *
     * @param Writer $csv
     * @param string $exportId
     *
     * @return int
     */
    private function exportProductsToCSV(Writer $csv, string $exportId): int
    {
        $products = $this->productService->getAll() ?? [];

        if (empty($products)) {
            $this->logger->error(__('messages.empty_products'), [
                'export_id' => $exportId,
            ]);
        }

        return $this->processProducts($csv, $products);
    }

    /**
     * Process all products and add to CSV
     *
     * @param Writer $csv
     * @param array<int, mixed> $products
     *
     * @return int
     */
    private function processProducts(Writer $csv, array $products): int
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
     * @param array<string, mixed> $productData
     * @return array<string|int|float>
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
            $productData['prices']['BYN'] ?? 0,
            $productData['image_url'],
        ];
    }
}
