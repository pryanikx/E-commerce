<?php

namespace App\Providers;

use App\Jobs\ExportCatalogJob;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class JobServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ExportCatalogJob::class, function ($app, array $parameters) {
            return new ExportCatalogJob(
                $parameters['exportId'] ?? '',
                $parameters['adminEmail'] ?? '',
                $app->make(LoggerInterface::class)
            );
        }, true);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
