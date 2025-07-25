<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ProductExportService;
use App\Services\ProductService;
use App\Services\S3UploadService;
use App\Services\Support\CsvWriterFactory;
use App\Services\Support\CsvWriterFactoryInterface;
use App\Services\Support\StorageServiceInterface;
use App\Services\Support\StorageUploaderInterface;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class CatalogExportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CsvWriterFactoryInterface::class, CsvWriterFactory::class);

        $this->app->singleton(ProductExportService::class, function ($app) {
            return new ProductExportService(
                $app->make(ProductService::class),
                $app->make(LoggerInterface::class),
                $app->make(CsvWriterFactoryInterface::class),
                $app->make(StorageServiceInterface::class),
                config('export.directory'),
                config('export.file_prefix'),
                config('export.file_extension'),
            );
        });

        $this->app->singleton(S3UploadService::class, function ($app) {
            return new S3UploadService(
                $app->make(LoggerInterface::class),
                config('aws.S3.bucket'),
                config('aws.S3.region'),
                config('aws.S3.version'),
                config('aws.S3.credentials.key'),
                config('aws.S3.credentials.secret'),
                config('aws.S3.endpoint'),
                config('aws.S3.use_path_style_endpoint'),
            );
        });

        $this->app->singleton(StorageUploaderInterface::class, S3UploadService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
