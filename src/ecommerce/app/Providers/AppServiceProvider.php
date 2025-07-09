<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoggerInterface::class, function ($app) {
            return $app->make('log');
        });
        // CacheInterface (PSR-16, Laravel compatible)
        $this->app->singleton(CacheInterface::class, function ($app) {
            return $app->make('cache')->store();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
