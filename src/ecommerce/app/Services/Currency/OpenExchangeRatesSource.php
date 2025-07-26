<?php

declare(strict_types=1);

namespace App\Services\Currency;

use App\Exceptions\Currency\CurrencyApiException;
use App\Services\Currency\Clients\Contracts\CurrencyApiClientInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class OpenExchangeRatesSource implements CurrencySource
{
    private const CACHE_DURATION_H = 24;
    private const DEFAULT_RATE = 1.0;

    /**
     * @param CurrencyApiClientInterface $client
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     * @param ClockInterface $clock
     * @param array<string> $supportedCurrencies
     */
    public function __construct(
        private readonly CurrencyApiClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
        private readonly ClockInterface $clock,
        private readonly array $supportedCurrencies,
    ) {
    }

    /**
     * Fetch exchange rates with base currency and caching.
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
                $rates = $this->client->fetchRates($baseCurrency);

                $this->logger->info(__('currency.rates_cached_successfully'), [
                    'base_currency' => $baseCurrency,
                    'cache_duration_hours' => self::CACHE_DURATION_H,
                    'rates_count' => count($rates),
                ]);

                return $rates;
            } catch (CurrencyApiException $e) {
                $this->logger->warning(__('currency.using_fallback_rates'), [
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'base_currency' => $baseCurrency,
                    'is_temporary' => $e->isTemporary(),
                ]);

                return $this->getFallbackRates();
            }
        });
    }

    /**
     * Get fallback rates when the API fails.
     *
     * @return array<string, float>
     */
    private function getFallbackRates(): array
    {
        $fallbackRates = array_map(
            fn () => self::DEFAULT_RATE,
            array_combine($this->supportedCurrencies, $this->supportedCurrencies)
        );

        $this->logger->info(__('currency.fallback_rates_generated'), [
            'supported_currencies' => $this->supportedCurrencies,
            'default_rate' => self::DEFAULT_RATE,
        ]);

        return $fallbackRates;
    }
}
