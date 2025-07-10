<?php

declare(strict_types=1);

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use App\Services\Support\HttpClientInterface;
use Psr\Clock\ClockInterface;

class OpenExchangeRatesSource implements CurrencySource
{
    private const CACHE_DURATION_H = 24;

    private const DEFAULT_RATE = 1.0;

    public function __construct(
        private readonly LoggerInterface     $logger,
        private readonly CacheInterface      $cache,
        private readonly HttpClientInterface $http,
        private readonly ClockInterface      $clock,
        private readonly string              $apiKey,
        private readonly string              $apiUrl,
        private readonly array               $supportedCurrencies,
    )
    {
    }

    /**
     * Fetch exchange rates with base currency.
     *
     * @param string $baseCurrency
     *
     * @return array<string, float>
     * @throws \Exception
     */
    public function getExchangeRates(string $baseCurrency): array
    {
        $cacheKey = "exchange_rates_{$baseCurrency}";
        $cacheDuration = $this->clock->now()
            ->add(new \DateInterval('PT' . self::CACHE_DURATION_H . 'H'));

        return $this->cache->remember($cacheKey, $cacheDuration, function () use ($baseCurrency) {
            try {
                $response = $this->http->get($this->apiUrl, [
                    'app_id' => $this->apiKey,
                    'base' => $baseCurrency,
                ]);

                if ($response->failed()) {
                    $this->logger->error(__('errors.fetch_exchange_rates_failed'), [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new \Exception(__('errors.fetch_exchange_rates_failed'));
                }

                $data = $response->json();

                return $data['rates'] ?? array_map(
                    fn() => self::DEFAULT_RATE,
                    array_combine($this->supportedCurrencies, $this->supportedCurrencies));
            } catch (\Exception $e) {
                $this->logger->error(__('errors.fetch_exchange_rates_failed'), [
                    'message' => $e->getMessage(),
                    'base_currency' => $baseCurrency,
                ]);

                return array_map(
                    fn() => self::DEFAULT_RATE,
                    array_combine($this->supportedCurrencies, $this->supportedCurrencies));
            }
        });
    }
}
