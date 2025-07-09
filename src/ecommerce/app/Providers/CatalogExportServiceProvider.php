<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\User\ProductController;
use App\Services\EmailNotificationService;
use App\Services\ProductExportService;
use App\Services\S3UploadService;
use Illuminate\Support\ServiceProvider;

class CatalogExportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductExportService::class, function ($app) {
            return new ProductExportService(
                $app->make(\App\Http\Controllers\User\ProductController::class),
                $app->make(\Psr\Log\LoggerInterface::class),
            );
        });

        $this->app->singleton(S3UploadService::class, function ($app) {
            return new S3UploadService(
                $app->make(\Psr\Log\LoggerInterface::class),
            );
        });

        $this->app->singleton(EmailNotificationService::class, function ($app) {
            return new EmailNotificationService(
                $app->make(\Psr\Log\LoggerInterface::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
