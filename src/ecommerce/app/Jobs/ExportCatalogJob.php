<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ProductExportService;
use App\Services\S3UploadService;
use App\Services\EmailNotificationService;
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

    public function __construct(
        protected readonly string $exportId,
        protected readonly string $adminEmail,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Execute the catalog export job
     *
     * @throws \Throwable
     */
    public function handle(
        ProductExportService $exportService,
        S3UploadService $s3Service,
        EmailNotificationService $emailService
    ): void {
        try {
            $this->logger->info(__('messages.export_started'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
            ]);

            $csvFilePath = $this->generateCsvFile($exportService);
            $s3Key = $this->uploadToS3($s3Service, $csvFilePath);
            $stats = $exportService->getExportStats();

            $this->sendSuccessNotification($emailService, $s3Key, $stats);

            $this->logger->info(__('messages.export_completed'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_S3_KEY => $s3Key,
                self::CONTEXT_STATS => $stats
            ]);

        } catch (\Throwable $exception) {
            $this->handleExportFailure($exception, $emailService);

            throw $exception;
        }
    }

    /**
     * Handle failed job execution
     *
     * @param \Throwable $exception
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
     * Generate CSV file from catalog data
     *
     * @param ProductExportService $exportService
     *
     * @return string
     * @throws \Exception
     */
    private function generateCsvFile(ProductExportService $exportService): string
    {
        $csvFilePath = $exportService->exportToCSV($this->exportId);

        if (!file_exists($csvFilePath)) {
            throw new \Exception(__('errors.csv_not_created') . ": {$csvFilePath}");
        }

        $this->logger->info(__('messages.csv_file_generated'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_FILE_PATH => $csvFilePath,
            self::CONTEXT_FILE_SIZE => filesize($csvFilePath)
        ]);

        return $csvFilePath;
    }

    /**
     * Upload CSV file to S3 storage
     *
     * @param S3UploadService $s3Service
     * @param string $csvFilePath
     *
     * @return string
     * @throws \Exception
     */
    private function uploadToS3(S3UploadService $s3Service, string $csvFilePath): string
    {
        $s3Key = $s3Service->uploadCatalogExport($csvFilePath, $this->exportId);

        if (!$s3Key) {
            throw new \Exception(__('errors.s3_upload_failed'));
        }

        $this->logger->info(__('messages.file_uploaded_to_s3'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_S3_KEY => $s3Key
        ]);

        return $s3Key;
    }

    /**
     * Send success notification email
     *
     * @param EmailNotificationService $emailService
     * @param string $s3Key
     * @param array $stats
     */
    private function sendSuccessNotification(
        EmailNotificationService $emailService,
        string $s3Key,
        array $stats
    ): void {
        $emailService->sendExportSuccessNotification(
            $this->adminEmail,
            $this->exportId,
            $s3Key,
            $stats
        );

        $this->logger->info(__('messages.success_notification_sent'), [
            self::CONTEXT_EXPORT_ID => $this->exportId,
            self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
        ]);
    }

    /**
     * Handle export failure and send notification
     *
     * @param \Throwable $exception
     * @param EmailNotificationService $emailService
    */
    private function handleExportFailure(\Throwable $exception, EmailNotificationService $emailService): void
    {
        $this->logger->error(__('messages.export_failed'), [
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

            $this->logger->info(__('messages.failure_notification_sent'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_ADMIN_EMAIL => $this->adminEmail
            ]);

        } catch (\Throwable $emailException) {
            $this->logger->error(__('messages.failure_notification_failed'), [
                self::CONTEXT_EXPORT_ID => $this->exportId,
                self::CONTEXT_EMAIL_ERROR => $emailException->getMessage()
            ]);
        }
    }
}
