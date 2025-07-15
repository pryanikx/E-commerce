<?php

declare(strict_types=1);

namespace App\Services\Email;

use Illuminate\Contracts\Filesystem\Filesystem;

class EmailFileLogger
{
    private const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

    public function __construct(
        private Filesystem $filesystem,
        private EmailHtmlBuilder $htmlBuilder,
        private EmailDirectoryManager $directoryManager,
        private string $emailLogPath,
        private string $emailFilePrefix,
        private string $fromEmail,
    ) {
        $this->directoryManager->ensureDirectoryExists($this->emailLogPath);
    }

    public function saveEmailToFile(string $to, string $subject, string $content, string $exportId): void
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

    private function saveHtmlVersion(string $to, string $subject, string $content, string $exportId): void
    {
        $htmlFilePath = $this->emailLogPath . "/" . $this->emailFilePrefix . "{$exportId}.html";
        $htmlContent = $this->htmlBuilder->buildHtmlWrapper($to, $subject, $content, $this->fromEmail);
        $this->filesystem->put($htmlFilePath, $htmlContent);
    }

    public function getEmailFilePath(string $exportId): string
    {
        return $this->emailLogPath . "/" . $this->emailFilePrefix . "{$exportId}.json";
    }
}
