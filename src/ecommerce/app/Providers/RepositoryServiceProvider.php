<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\LoginRepositoryInterface;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use App\Repositories\Contracts\ManufacturerRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use App\Repositories\LoginRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RegisterRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(
            MaintenanceRepositoryInterface::class,
            MaintenanceRepository::class
        );
        $this->app->bind(
            ManufacturerRepositoryInterface::class,
            ManufacturerRepository::class
        );
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
        $this->app->bind(
            LoginRepositoryInterface::class,
            LoginRepository::class
        );
        $this->app->bind(
            RegisterRepositoryInterface::class,
            RegisterRepository::class
        );
        $this->app->bind(
            \Illuminate\Contracts\Auth\Factory::class,
            function ($app) {
                return $app['auth'];
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
