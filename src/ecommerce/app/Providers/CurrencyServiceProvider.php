<?php

namespace App\Providers;

use App\Services\Currency\CurrencyCalculatorService;
use App\Services\Currency\CurrencySource;
use App\Services\Currency\OpenExchangeRatesSource;
use Illuminate\Support\ServiceProvider;
use App\Services\Support\HttpClientInterface;
use App\Services\Support\LaravelHttpClient;

class CurrencyServiceProvider extends ServiceProvider
{
    private const DEFAULT_CURRENCY_BASE = 'USD';

    /**
     * Register currency-related services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(HttpClientInterface::class, LaravelHttpClient::class);

        $this->app->singleton(CurrencySource::class, function ($app) {
            return new OpenExchangeRatesSource(
                $app->make(\Psr\Log\LoggerInterface::class),
                $app->make(\Illuminate\Contracts\Cache\Repository::class),
                $app->make(HttpClientInterface::class),
                $app->make(\Psr\Clock\ClockInterface::class),
                config('services.open_exchange_rates.api_key'),
                config(
                    'services.open_exchange_rates.api_url',
                    'https://openexchangerates.org/api/latest.json'
                ),
                config(
                    'services.open_exchange_rates.supported_currencies',
                    ['BYN', 'USD', 'EUR', 'RUB']
                )
            );
        });

        $this->app->singleton(CurrencyCalculatorService::class, function ($app) {
            return new CurrencyCalculatorService(
                $app->make(CurrencySource::class),
                config('services.currency.base', 'USD'),
                config(
                    'services.open_exchange_rates.supported_currencies',
                    ['BYN', 'USD', 'EUR', 'RUB']
                )
            );
        });
    }

    /**
     * Bootstrap currency-related services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
