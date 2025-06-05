<?php

namespace App\Providers;

use App\Services\Currency\CurrencyCalculator;
use App\Services\Currency\CurrencySource;
use App\Services\Currency\OpenExchangeRatesSource;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencySource::class, function ($app) {
            return new OpenExchangeRatesSource();
        });

        $this->app->singleton(CurrencyCalculator::class, function ($app) {
            return new CurrencyCalculator(
                $app->make(CurrencySource::class),
                config('services.currency.base', 'USD')
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
