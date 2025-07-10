<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class EmailNotificationService
{
    private const DIRECTORY_PERMISSIONS = 0755;
    private const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

    public function __construct(
        private LoggerInterface $logger,
        private Filesystem $filesystem,
        private string $fromEmail,
        private string $emailLogPath,
        private string $emailFilePrefix,
    ) {
        $this->ensureDirectoryExists();
    }

    /**
     * Send export success notification email
     *
     * @param string $adminEmail
     * @param string $exportId
     * @param string $s3Key
     * @param array<string, mixed> $stats
     * @return bool
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

            $this->saveEmailToFile($adminEmail, $subject, $emailContent, $exportId);

            $this->logger->info(__('messages.email_notification_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->getEmailFilePath($exportId)
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
     *
     * @param string $adminEmail
     * @param string $exportId
     * @param string $errorMessage
     *
     * @return bool
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
            $this->saveEmailToFile($adminEmail, $subject, $emailContent, $errorFileId);

            $this->logger->info(__('messages.error_email_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->getEmailFilePath($errorFileId)
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
     * Ensure email log directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!$this->filesystem->exists($this->emailLogPath)) {
            $this->filesystem->makeDirectory($this->emailLogPath, self::DIRECTORY_PERMISSIONS, true);
        }
    }

    /**
     * Save email to file
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $exportId
     */
    private function saveEmailToFile(string $to, string $subject, string $content, string $exportId): void
    {
        $filePath = $this->getEmailFilePath($exportId);

        $emailData = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'to' => $to,
            'from' => $this->fromEmail,
            'subject' => $subject,
            'content' => $content
        ];

        $this->filesystem->put($filePath, json_encode($emailData, self::JSON_FLAGS));

        $this->saveHtmlVersion($to, $subject, $content, $exportId);
    }

    /**
     * Save HTML version of email
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $exportId
     */
    private function saveHtmlVersion(string $to, string $subject, string $content, string $exportId): void
    {
        $htmlFilePath = $this->emailLogPath . "/" . $this->emailFilePrefix . "{$exportId}.html";
        $htmlContent = $this->buildHtmlWrapper($to, $subject, $content);
        $this->filesystem->put($htmlFilePath, $htmlContent);
    }

    /**
     * Build HTML wrapper for email
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @return string
     */
    private function buildHtmlWrapper(string $to, string $subject, string $content): string
    {
        return view('emails.html_wrapper', [
            'fromEmail' => $this->fromEmail,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
        ])->render();
    }

    /**
     * Get email file path
     *
     * @param string $exportId
     * @return string
     */
    private function getEmailFilePath(string $exportId): string
    {
        return $this->emailLogPath . "/" . $this->emailFilePrefix . "{$exportId}.json";
    }

    /**
     * Build success email content
     *
     * @param string $exportId
     * @param string $s3Key
     * @param array $stats
     * @return string
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
     *
     * @param string $exportId
     * @param string $errorMessage
     * @return string
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
