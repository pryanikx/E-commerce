<?php

declare(strict_types=1);

namespace App\Services\Email;

use App\DTO\Email\EmailDTO;
use App\Services\Support\StorageServiceInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Clock\ClockInterface;

class EmailFileLogger
{
    private const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

    /**
     * @param Filesystem $filesystem
     * @param EmailHtmlBuilder $htmlBuilder
     * @param EmailDirectoryManager $directoryManager
     * @param ClockInterface $clock
     * @param StorageServiceInterface $storageService
     * @param string $emailLogDirectory
     * @param string $emailFilePrefix
     * @param string $fromEmail
     */
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly EmailHtmlBuilder $htmlBuilder,
        private readonly EmailDirectoryManager $directoryManager,
        private readonly ClockInterface $clock,
        private readonly StorageServiceInterface $storageService,
        private readonly string $emailLogDirectory,
        private readonly string $emailFilePrefix,
        private readonly string $fromEmail,
    ) {
        $this->directoryManager->ensureDirectoryExists($this->storageService->path($this->emailLogDirectory));
    }

    /**
     * Save email to file.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $exportId
     *
     * @return void
     * @throws \Throwable
     */
    public function saveEmailToFile(string $to, string $subject, string $content, string $exportId): void
    {
        $filePath = $this->getEmailFilePath($exportId);

        $emailData = new EmailDTO(
            timestamp: $this->clock->now()->format('Y-m-d H:i:s'),
            to: $to,
            from: $this->fromEmail,
            subject: $subject,
            content: $content,
        );

        $jsonContent = json_encode($emailData, self::JSON_FLAGS);
        $this->filesystem->put($filePath, $jsonContent !== false ? $jsonContent : '{}');
        $this->saveHtmlVersion($to, $subject, $content, $exportId);
    }

    /**
     * Save email in HTML version.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $exportId
     *
     * @return void
     * @throws \Throwable
     */
    private function saveHtmlVersion(string $to, string $subject, string $content, string $exportId): void
    {
        $htmlFilePath = $this->storageService->path(
            $this->emailLogDirectory . "/" . $this->emailFilePrefix . "{$exportId}.html"
        );
        $htmlContent = $this->htmlBuilder->buildHtmlWrapper(
            $to,
            $subject,
            $content,
            $this->fromEmail
        );
        $this->filesystem->put($htmlFilePath, $htmlContent);
    }

    /**
     * Get an email file path.
     *
     * @param string $exportId
     *
     * @return string
     */
    public function getEmailFilePath(string $exportId): string
    {
        return $this->storageService->path(
            $this->emailLogDirectory . "/" . $this->emailFilePrefix . "{$exportId}.json"
        );
    }
}
