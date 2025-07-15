<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ProductExportService;
use App\Services\S3UploadService;
use App\Services\Support\StorageUploaderInterface;
use App\Services\Email\EmailNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Log\LoggerInterface;

class ExportCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CONTEXT_EXPORT_ID = 'export_id';
    private const CONTEXT_ADMIN_EMAIL = 'admin_email';
    private const CONTEXT_FILE_PATH = 'file_path';
    private const CONTEXT_FILE_SIZE = 'file_size';
    private const CONTEXT_S3_KEY = 's3_key';
    private const CONTEXT_STATS = 'stats';
    private const CONTEXT_ERROR = 'error';
    private const CONTEXT_TRACE = 'trace';
    private const CONTEXT_ATTEMPTS = 'attempts';
    private const CONTEXT_EMAIL_ERROR = 'email_error';

    public int $timeout = 600;
    public int $tries = 3;

    /**
     * @param string $exportId
     * @param string $adminEmail
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected readonly string $exportId,
        protected readonly string $adminEmail,
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * Execute the catalog export job.
     *
     * @param ProductExportService $exportService
     * @param StorageUploaderInterface $uploader
     * @param EmailNotificationService $emailService
     *
     * @return void
     * @throws \Throwable
     */
    public function handle(
        ProductExportService $exportService,
        StorageUploaderInterface $uploader,
        EmailNotificationService $emailService,
    ): void {
        try {
            $this->logger->info(__('messages.export_started'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
            ]);

            $csvFilePath = $this->generateCsvFile($exportService, $logger);
            $storageKey = $this->uploadToStorage($uploader, $csvFilePath, $logger);
            $stats = $exportService->getExportStats();

            $this->sendSuccessNotification($emailService, $storageKey, $stats, $logger);

            $logger->info(__('messages.export_completed'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_S3_KEY => $storageKey,
                self::CONTEXT_STATS => $stats
            ]);

        } catch (\Throwable $exception) {
            $this->handleExportFailure($emailService, $exception, $this->logger);

            throw $exception;
        }
    }

    /**
     * Handle failed job execution.
     *
     * @param \Throwable $exception
     *
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        $this->logger->error(__('messages.export_failed_permanently'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_ADMIN_EMAIL => $this->adminEmail,
            self::CONTEXT_ERROR => $exception->getMessage(),
            self::CONTEXT_ATTEMPTS => $this->attempts()
        ]);
    }

    /**
     * Generate CSV file from catalog data.
     *
     * @param ProductExportService $exportService
     * @param LoggerInterface $logger
     *
     * @return string
     * @throws \Exception
     */
    private function generateCsvFile(ProductExportService $exportService, LoggerInterface $logger): string
    {
        $csvFilePath = $exportService->exportToCSV($this->exportId);

        if (!file_exists($csvFilePath)) {
            throw new \Exception(__('errors.csv_not_created') . ": {$csvFilePath}");
        }

        $logger->info(__('messages.csv_file_generated'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_FILE_PATH => $csvFilePath,
            self::CONTEXT_FILE_SIZE => filesize($csvFilePath)
        ]);

        return $csvFilePath;
    }

    /**
     * Upload CSV file to S3 storage.
     *
     * @param StorageUploaderInterface $uploader
     * @param string $csvFilePath
     * @param LoggerInterface $logger
     *
     * @return string
     * @throws \Exception
     */
    private function uploadToStorage(StorageUploaderInterface $uploader, string $csvFilePath, LoggerInterface $logger): string
    {
        $storageKey = $uploader->uploadCatalogExport($csvFilePath, $this->exportId);

        if (!$storageKey) {
            throw new \Exception(__('errors.storage_upload_failed'));
        }

        $logger->info(__('messages.file_uploaded_to_storage'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_S3_KEY => $storageKey
        ]);

        return $storageKey;
    }

    /**
     * Send success notification email.
     *
     * @param EmailNotificationService $emailService
     * @param string $storageKey
     * @param array $stats
     * @param LoggerInterface $logger
     *
     * @return void
     */
    private function sendSuccessNotification(
        EmailNotificationService $emailService,
        string $storageKey,
        array $stats,
        LoggerInterface $logger
    ): void {
        $emailService->sendExportSuccessNotification(
            $this->adminEmail,
            $this->exportId,
            $storageKey,
            $stats
        );

        $logger->info(__('messages.success_notification_sent'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
        ]);
    }

    /**
     * Handle export failure and send notification.
     *
     * @param EmailNotificationService $emailService
     * @param \Throwable $exception
     * @param LoggerInterface $logger
     *
     * @return void
     */
    private function handleExportFailure(
        EmailNotificationService $emailService,
        \Throwable $exception,
        LoggerInterface $logger
    ): void
    {
        $logger->error(__('messages.export_failed'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_ERROR => $exception->getMessage(),
            self::CONTEXT_TRACE => $exception->getTraceAsString()
        ]);

        try {
            $emailService->sendExportFailureNotification(
                $this->adminEmail,
                $this->exportId,
                $exception->getMessage()
            );

            $logger->info(__('messages.failure_notification_sent'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
            ]);

        } catch (\Throwable $emailException) {
            $logger->error(__('messages.failure_notification_failed'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_EMAIL_ERROR => $emailException->getMessage()
            ]);
        }
    }
}
