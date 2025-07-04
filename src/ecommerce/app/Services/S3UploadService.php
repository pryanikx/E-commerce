<?php

declare(strict_types=1);

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3UploadService
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
    protected string $bucket;

    public function __construct()
    {
        $this->bucket = $this->initializeS3Bucket();

        $this->s3Client = $this->initializeS3Client();
    }

    /**
     * Initializes S3 Bucket
     *
     * @return string
     */
    public function initializeS3Bucket(): string
    {
        return config('aws.S3.bucket');
    }

    /**
     * Initializes S3Client
     *
     * @return S3Client
     */
    public function initializeS3Client(): S3Client
    {
        return new S3Client([
            'version' => config('aws.S3.version'),
            'region' => config('aws.S3.region'),
            'credentials' => [
                'key' => config('aws.S3.credentials.key'),
                'secret' => config('aws.S3.credentials.secret'),
            ],
            'endpoint' => config('aws.S3.endpoint'),
            'use_path_style_endpoint' => config('aws.S3.use_path_style_endpoint'),
        ]);
    }

    /**
     * Upload catalog export file to S3 storage
     *
     * @param string $filePath
     * @param string $exportId
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
                logger()->info(__('messages.csv_uploaded'), [
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
            logger()->error(__('messages.s3_aws_error'), [
                self::CONTEXT_EXPORT_ID => $exportId,
                self::CONTEXT_ERROR_CODE => $e->getAwsErrorCode(),
                self::CONTEXT_ERROR_MESSAGE => $e->getAwsErrorMessage(),
                self::CONTEXT_FILE_PATH => $filePath
            ]);

            return null;

        } catch (\Exception $e) {
            logger()->error(__('messages.s3_general_error'), [
                self::CONTEXT_EXPORT_ID => $exportId,
                self::CONTEXT_ERROR => $e->getMessage(),
                self::CONTEXT_FILE_PATH => $filePath
            ]);

            return null;
        }
    }

    /**
     * Check if file exists in S3 storage
     *
     * @param string $s3Key
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

            logger()->error(__('messages.s3_file_check_error'), [
                self::CONTEXT_S3_KEY => $s3Key,
                self::CONTEXT_ERROR => $e->getMessage()
            ]);

            return false;
        }
    }
}
