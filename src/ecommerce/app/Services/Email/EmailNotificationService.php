<?php

declare(strict_types=1);

namespace App\Services\Email;

use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class EmailNotificationService
{
    /**
     * @param LoggerInterface $logger
     * @param EmailFileLogger $fileLogger
     * @param ClockInterface $clock
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EmailFileLogger $fileLogger,
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * Send export success notification email.
     *
     * @param string $adminEmail
     * @param string $exportId
     * @param string $s3Key ,
     * @param array $stats
     *
     * @return void
     * @throws \Throwable
     */
    public function sendExportSuccessNotification(
        string $adminEmail,
        string $exportId,
        string $s3Key,
        array $stats
    ): void {
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
        } catch (\Exception $e) {
            $this->logger->error(__('errors.export_success_notification_failed'), [
                'export_id' => $exportId,
                'admin_email' => $adminEmail,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Send export failure notification email.
     *
     * @param string $adminEmail
     * @param string $exportId
     * @param string $errorMessage
     *
     * @return void
     * @throws \Throwable
     */
    public function sendExportFailureNotification(
        string $adminEmail,
        string $exportId,
        string $errorMessage
    ): void {
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
        } catch (\Exception $e) {
            $this->logger->error(__('errors.export_failure_notification_failed'), [
                'export_id' => $exportId,
                'admin_email' => $adminEmail,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Build success email content.
     *
     * @param string $exportId
     * @param string $s3Key
     * @param array $stats
     *
     * @return string
     * @throws \Throwable
     */
    private function buildSuccessEmail(string $exportId, string $s3Key, array $stats): string
    {
        $currentTime = $this->clock->now()->format('d.m.Y H:i:s');

        return view('emails.export_success', [
            'exportId' => $exportId,
            's3Key' => $s3Key,
            'stats' => $stats,
            'currentTime' => $currentTime,
        ])->render();
    }

    /**
     * Build failure email content.
     *
     * @param string $exportId
     * @param string $errorMessage
     *
     * @return string
     * @throws \Throwable
     */
    private function buildFailureEmail(string $exportId, string $errorMessage): string
    {
        $currentTime = $this->clock->now()->format('d.m.Y H:i:s');

        return view('emails.export_failure', [
            'exportId' => $exportId,
            'errorMessage' => $errorMessage,
            'currentTime' => $currentTime,
        ])->render();
    }
}
