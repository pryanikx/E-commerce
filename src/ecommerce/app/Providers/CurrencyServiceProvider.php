<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Currency\Clients\Contracts\CurrencyApiClientInterface;
use App\Services\Currency\Clients\OpenExchangeRatesClient;
use App\Services\Currency\CurrencyCalculatorService;
use App\Services\Currency\CurrencySource;
use App\Services\Currency\OpenExchangeRatesSource;
use App\Services\Support\HttpClientInterface;
use App\Services\Support\LaravelHttpClient;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HttpClientInterface::class, LaravelHttpClient::class);

        $this->app->bind(CurrencyApiClientInterface::class, function ($app) {
            return new OpenExchangeRatesClient(
                $app->make(HttpClientInterface::class),
                $app->make(LoggerInterface::class),
                config('services.open_exchange_rates.api_key'),
                config(
                    'services.open_exchange_rates.api_url',
                    'https://openexchangerates.org/api/latest.json'
                ),
            );
        });

        $this->app->bind(CurrencySource::class, function ($app) {
            return new OpenExchangeRatesSource(
                $app->make(CurrencyApiClientInterface::class),
                $app->make(LoggerInterface::class),
                $app->make(Repository::class),
                $app->make(ClockInterface::class),
                config(
                    'services.open_exchange_rates.supported_currencies',
                    ['BYN', 'USD', 'EUR', 'RUB']
                ),
            );
        });

        // Калькулятор валют
        $this->app->singleton(CurrencyCalculatorService::class, function ($app) {
            return new CurrencyCalculatorService(
                $app->make(CurrencySource::class),
                config('services.currency.base', 'USD'),
                config(
                    'services.open_exchange_rates.supported_currencies',
                    ['BYN', 'USD', 'EUR', 'RUB']
                ),
            );
        });
    }
}
