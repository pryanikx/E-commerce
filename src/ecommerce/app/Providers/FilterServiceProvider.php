<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Filters\ProductFilter;
use App\Services\Filters\ProductSorter;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ProductFilter::class, function () {
            return new ProductFilter();
        });

        $this->app->singleton(ProductSorter::class, function () {
            return new ProductSorter();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
