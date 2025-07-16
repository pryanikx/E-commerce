<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ProductExportService;
use App\Services\S3UploadService;
use App\Services\Support\CsvWriterFactory;
use App\Services\Support\CsvWriterFactoryInterface;
use App\Services\Support\StorageUploaderInterface;
use Illuminate\Support\ServiceProvider;

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
                $app->make(\App\Services\ProductService::class),
                $app->make(\Psr\Log\LoggerInterface::class),
                $app->make(\App\Services\Support\CsvWriterFactoryInterface::class),
                config('export.directory'),
                config('export.file_prefix'),
                config('export.file_extension'),
            );
        });

        $this->app->singleton(S3UploadService::class, function ($app) {
            return new S3UploadService(
                $app->make(\Psr\Log\LoggerInterface::class),
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
