<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

class EmailNotificationService
{
    private const EMAIL_LOG_DIRECTORY = 'app/emails';
    private const EMAIL_FILE_PREFIX = 'email_';
    private const DIRECTORY_PERMISSIONS = 0755;
    private const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
    private const DEFAULT_FROM_EMAIL = 'noreply@example.com';
    private const SUCCESS_ICON = '✅';
    private const ERROR_ICON = '❌';
    private const EMAIL_ICON = '📧';
    private const DETAILS_ICON = '📋';
    private const STATS_ICON = '📊';
    private const WARNING_ICON = '⚠️';
    private const CELEBRATION_ICON = '🎉';

    protected string $fromEmail;
    protected string $emailLogPath;

    public function __construct()
    {
        $this->fromEmail = env('CATALOG_EXPORT_FROM_EMAIL', self::DEFAULT_FROM_EMAIL);
        $this->emailLogPath = storage_path(self::EMAIL_LOG_DIRECTORY);

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

            logger()->info(__('messages.email_notification_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->getEmailFilePath($exportId)
            ]);

            return true;

        } catch (\Exception $e) {
            logger()->error(__('errors.export_success_notification_failed'), [
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

            logger()->info(__('messages.error_email_saved'), [
                'to' => $adminEmail,
                'subject' => $subject,
                'export_id' => $exportId,
                'file_path' => $this->getEmailFilePath($errorFileId)
            ]);

            return true;

        } catch (\Exception $e) {
            logger()->error(__('errors.export_failure_notification_failed'), [
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
        if (!File::exists($this->emailLogPath)) {
            File::makeDirectory($this->emailLogPath, self::DIRECTORY_PERMISSIONS, true);
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

        File::put($filePath, json_encode($emailData, self::JSON_FLAGS));

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
        $htmlFilePath = $this->emailLogPath . "/" . self::EMAIL_FILE_PREFIX . "{$exportId}.html";
        $htmlContent = $this->buildHtmlWrapper($to, $subject, $content);

        File::put($htmlFilePath, $htmlContent);
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
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$subject}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .email-header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                .email-content { line-height: 1.6; }
            </style>
        </head>
        <body>
            <div class='email-header'>
                <strong>" . __('notifications.from') . ":</strong> {$this->fromEmail}<br>
                <strong>" . __('notifications.to') . ":</strong> {$to}<br>
                <strong>" . __('notifications.subject') . ":</strong> {$subject}<br>
                <strong>" . __('notifications.time') . ":</strong> " . now()->format('d.m.Y H:i:s') . "
            </div>
            <div class='email-content'>
                {$content}
            </div>
        </body>
        </html>";
    }

    /**
     * Get email file path
     *
     * @param string $exportId
     * @return string
     */
    private function getEmailFilePath(string $exportId): string
    {
        return $this->emailLogPath . "/" . self::EMAIL_FILE_PREFIX . "{$exportId}.json";
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

        return "
        <div style='font-family: Arial, sans-serif;'>
            <h2 style='color: #28a745;'>" . self::SUCCESS_ICON . " " . __('notifications.export_completed_successfully') . "</h2>

            <div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3>" . self::DETAILS_ICON . " " . __('notifications.export_details') . ":</h3>
                <ul>
                    <li><strong>" . __('notifications.export_id') . ":</strong> {$exportId}</li>
                    <li><strong>" . __('notifications.storage_file') . ":</strong> {$s3Key}</li>
                    <li><strong>" . __('notifications.export_time') . ":</strong> {$currentTime}</li>
                </ul>
            </div>

            <div style='background-color: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3>" . self::STATS_ICON . " " . __('notifications.export_statistics') . ":</h3>
                <ul>
                    <li><strong>" . __('notifications.total_products_exported') . ":</strong> " . ($stats['total_products'] ?? 0) . "</li>
                    <li><strong>" . __('notifications.products_with_images') . ":</strong> " . ($stats['products_with_images'] ?? 0) . "</li>
                    <li><strong>" . __('notifications.products_with_manufacturer') . ":</strong> " . ($stats['products_with_manufacturer'] ?? 0) . "</li>
                    <li><strong>" . __('notifications.products_with_category') . ":</strong> " . ($stats['products_with_category'] ?? 0) . "</li>
                </ul>
            </div>

            <p style='color: #155724;'>
                " . self::CELEBRATION_ICON . " " . __('notifications.export_success_message') . "
            </p>

            <hr style='margin: 30px 0;'>
            <p style='color: #6c757d; font-size: 12px;'>
                " . __('notifications.automatic_notification') . "
                " . __('notifications.sent_time') . ": {$currentTime}
            </p>
        </div>";
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

        return "
        <div style='font-family: Arial, sans-serif;'>
            <h2 style='color: #dc3545;'>" . self::ERROR_ICON . " " . __('notifications.catalog_export_error') . "</h2>

            <div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3>" . self::WARNING_ICON . " " . __('notifications.error_details') . ":</h3>
                <ul>
                    <li><strong>" . __('notifications.export_id') . ":</strong> {$exportId}</li>
                    <li><strong>" . __('notifications.error_time') . ":</strong> {$currentTime}</li>
                    <li><strong>" . __('notifications.error_description') . ":</strong> {$errorMessage}</li>
                </ul>
            </div>

            <p style='color: #721c24;'>
                " . __('notifications.export_failure_message') . "
            </p>

            <hr style='margin: 30px 0;'>
            <p style='color: #6c757d; font-size: 12px;'>
                " . __('notifications.automatic_notification') . "
                " . __('notifications.sent_time') . ": {$currentTime}
            </p>
        </div>";
    }
}
