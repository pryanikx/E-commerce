<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Support\ServiceProvider;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\NativeClock;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(LoggerInterface::class, function ($app) {
            return $app->make('log');
        });
        $this->app->singleton(CacheInterface::class, function ($app) {
            return $app->make('cache')->store();
        });
        $this->app->singleton(ClockInterface::class, function () {
            return new NativeClock();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
