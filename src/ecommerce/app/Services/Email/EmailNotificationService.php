<?php

declare(strict_types=1);

namespace App\Services\Email;

use Psr\Log\LoggerInterface;

class EmailNotificationService
{
    public function __construct(
        private LoggerInterface $logger,
        private EmailFileLogger $fileLogger,
    ) {}

    /**
     * Send export success notification email
     */
    public function sendExportSuccessNotification(
        string $adminEmail,
        string $exportId,
        string $s3Key,
        array $stats
    ): bool {
        try {
            $subject = __('notifications.export_success_subject', ['export_id' => $exportId]);
            $emailContent = $this->buildSuccessEmail($exportId, $s3Key, $stats);

            $this->fileLogger->saveEmailToFile($adminEmail, $subject, $emailContent, $exportId);

            $this->logger->info(__('messages.success_email_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->fileLogger->getEmailFilePath($exportId)
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error(__('errors.export_success_notification_failed'), [
                'export_id' => $exportId,
                'admin_email' => $adminEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send export failure notification email
     */
    public function sendExportFailureNotification(
        string $adminEmail,
        string $exportId,
        string $errorMessage
    ): bool {
        try {
            $subject = __('notifications.export_failure_subject', ['export_id' => $exportId]);
            $emailContent = $this->buildFailureEmail($exportId, $errorMessage);

            $errorFileId = $exportId . '_error';
            $this->fileLogger->saveEmailToFile($adminEmail, $subject, $emailContent, $errorFileId);

            $this->logger->info(__('messages.error_email_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->fileLogger->getEmailFilePath($errorFileId)
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error(__('errors.export_failure_notification_failed'), [
                'export_id' => $exportId,
                'admin_email' => $adminEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Build success email content
     */
    private function buildSuccessEmail(string $exportId, string $s3Key, array $stats): string
    {
        $currentTime = now()->format('d.m.Y H:i:s');
        return view('emails.export_success', [
            'exportId' => $exportId,
            's3Key' => $s3Key,
            'stats' => $stats,
            'currentTime' => $currentTime,
        ])->render();
    }

    /**
     * Build failure email content
     */
    private function buildFailureEmail(string $exportId, string $errorMessage): string
    {
        $currentTime = now()->format('d.m.Y H:i:s');
        return view('emails.export_failure', [
            'exportId' => $exportId,
            'errorMessage' => $errorMessage,
            'currentTime' => $currentTime,
        ])->render();
    }
} 