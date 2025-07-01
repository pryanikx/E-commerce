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
                $app->make(ProductController::class)
            );
        });

        $this->app->singleton(S3UploadService::class, function ($app) {
            return new S3UploadService();
        });

        $this->app->singleton(EmailNotificationService::class, function ($app) {
            return new EmailNotificationService();
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
