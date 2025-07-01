<?php

namespace App\Providers;

use App\Services\Currency\CurrencyCalculator;
use App\Services\Currency\CurrencySource;
use App\Services\Currency\OpenExchangeRatesSource;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    private const DEFAULT_CURRENCY_BASE = 'USD';

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
                config('services.currency.base', self::DEFAULT_CURRENCY_BASE)
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
