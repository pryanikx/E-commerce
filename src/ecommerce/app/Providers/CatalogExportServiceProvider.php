<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\User\ProductController;
use App\Services\EmailNotificationService;
use App\Services\ProductExportService;
use App\Services\S3UploadService;
use Illuminate\Support\ServiceProvider;
use App\Services\Support\CsvWriterFactoryInterface;
use App\Services\Support\CsvWriterFactory;
use App\Services\Support\StorageUploaderInterface;

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
                $app->make(\App\Http\Controllers\User\ProductController::class),
                $app->make(\Psr\Log\LoggerInterface::class),
                $app->make(CsvWriterFactoryInterface::class),
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

        $this->app->singleton(EmailNotificationService::class, function ($app) {
            return new EmailNotificationService(
                $app->make(\Psr\Log\LoggerInterface::class),
                $app->make(\Illuminate\Contracts\Filesystem\Filesystem::class),
                config('services.email_notification.default_from_email', 'noreply@example.com'),
                storage_path(config('services.email_notification.email_log_directory', 'app/emails')),
                config('services.email_notification.email_file_prefix', 'email_'),
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
