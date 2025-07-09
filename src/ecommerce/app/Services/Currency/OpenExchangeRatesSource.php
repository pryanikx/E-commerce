<?php

declare(strict_types=1);

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class OpenExchangeRatesSource implements CurrencySource
{
    private const CACHE_DURATION_H = 24;

    private const DEFAULT_RATE = 1.0;

    /**
     * @var string $apiKey
     */
    protected string $apiKey;

    /**
     * @var string $apiUrl
     */
    protected string $apiUrl;

    public function __construct(
        private LoggerInterface $logger,
        private CacheInterface $cache,
    )
    {
        $this->apiKey = config('services.open_exchange_rates.api_key');
        $this->apiUrl = config('services.open_exchange_rates.api_url', 'https://openexchangerates.org/api/latest.json');
    }

    /**
     * Fetch exchange rates with base currency.
     *
     * @param string $baseCurrency
     * @return array<string, float>
     * @throws \Exception
     */
    public function getExchangeRates(string $baseCurrency): array
    {
        $cacheKey = "exchange_rates_{$baseCurrency}";
        $cacheDuration = now()->addHours(self::CACHE_DURATION_H);

        return $this->cache->remember($cacheKey, $cacheDuration, function () use ($baseCurrency) {
            try {
                $response = Http::get($this->apiUrl, [
                    'app_id' => $this->apiKey,
                    'base' => $baseCurrency,
                ]);

                if ($response->failed()) {
                    $this->logger->error(__('errors.fetch_exchange_rates_failed'), [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new \RuntimeException(__('errors.fetch_exchange_rates_failed'));
                }

                $data = $response->json();

                return $data['rates'] ?? array_map(fn() => self::DEFAULT_RATE, array_combine($this->getSupportedCurrencies(), $this->getSupportedCurrencies()));
            } catch (\Exception $e) {
                $this->logger->error(__('errors.fetch_exchange_rates_failed'), [
                    'message' => $e->getMessage(),
                    'base_currency' => $baseCurrency,
                ]);

                return array_map(fn() => self::DEFAULT_RATE, array_combine($this->getSupportedCurrencies(), $this->getSupportedCurrencies()));
            }
        });
    }

    public function getSupportedCurrencies(): array
    {
        return config('services.open_exchange_rates.supported_currencies', ['BYN', 'USD', 'EUR', 'RUB']);
    }

    public function getBaseCurrency(): string
    {
        return config('services.open_exchange_rates.base_currency', 'USD');
    }
}
