<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Support\StorageUploaderInterface;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;

class S3UploadService implements StorageUploaderInterface
{
    private const SUCCESS_STATUS_CODE = 200;
    private const NOT_FOUND_ERROR = 'NotFound';
    private const CONTEXT_EXPORT_ID = 'export_id';
    private const CONTEXT_S3_KEY = 's3_key';
    private const CONTEXT_BUCKET = 'bucket';
    private const CONTEXT_FILE_SIZE = 'file_size';
    private const CONTEXT_FILE_PATH = 'file_path';
    private const CONTEXT_ERROR_CODE = 'error_code';
    private const CONTEXT_ERROR_MESSAGE = 'error_message';
    private const CONTEXT_ERROR = 'error';

    protected S3Client $s3Client;

    public function __construct(
        private LoggerInterface $logger,
        private string $bucket,
        private string $region,
        private string $version,
        private string $key,
        private string $secret,
        private string $endpoint,
        private bool $usePathStyleEndpoint,
    ) {
        $this->s3Client = new S3Client([
            'version' => $this->version,
            'region' => $this->region,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ],
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => $this->usePathStyleEndpoint,
        ]);
    }

    /**
     * Upload a catalog export file to S3 storage.
     *
     * @param string $filePath
     * @param string $exportId
     *
     * @return string|null
     * @throws \Exception
     */
    public function uploadCatalogExport(string $filePath, string $exportId): ?string
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception(__('errors.csv_file_not_found') . ": {$filePath}");
            }

            $fileName = basename($filePath);
            $s3Key = "catalog-exports/" . date('Y/m/d') . "/{$fileName}";

            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
                'SourceFile' => $filePath,
                'ContentType' => 'text/csv',
                'Metadata' => [
                    self::CONTEXT_EXPORT_ID => $exportId,
                    'created_at' => now()->toISOString(),
                ],
            ]);

            if ($result['@metadata']['statusCode'] === self::SUCCESS_STATUS_CODE) {
                $this->logger->info(__('messages.csv_uploaded'), [
                    self::CONTEXT_EXPORT_ID => $exportId,
                    self::CONTEXT_S3_KEY => $s3Key,
                    self::CONTEXT_BUCKET => $this->bucket,
                    self::CONTEXT_FILE_SIZE => filesize($filePath)
                ]);

                return $s3Key;
            }

            throw new \Exception(
                __('errors.s3_upload_failed') . ": " . $result['@metadata']['statusCode']
            );
        } catch (AwsException $e) {
            $this->logger->error(__('messages.s3_aws_error'), [
                self::CONTEXT_EXPORT_ID => $exportId,
                self::CONTEXT_ERROR_CODE => $e->getAwsErrorCode(),
                self::CONTEXT_ERROR_MESSAGE => $e->getAwsErrorMessage(),
                self::CONTEXT_FILE_PATH => $filePath
            ]);

            return null;
        } catch (\Exception $e) {
            $this->logger->error(__('messages.s3_general_error'), [
                self::CONTEXT_EXPORT_ID => $exportId,
                self::CONTEXT_ERROR => $e->getMessage(),
                self::CONTEXT_FILE_PATH => $filePath
            ]);

            return null;
        }
    }

    /**
     * Check if file exists in S3 storage.
     *
     * @param string $s3Key
     *
     * @return bool
     */
    public function fileExists(string $s3Key): bool
    {
        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
            ]);

            return true;
        } catch (AwsException $e) {
            if ($e->getAwsErrorCode() === self::NOT_FOUND_ERROR) {
                return false;
            }

            $this->logger->error(__('messages.s3_file_check_error'), [
                self::CONTEXT_S3_KEY => $s3Key,
                self::CONTEXT_ERROR => $e->getMessage()
            ]);

            return false;
        }
    }
}
