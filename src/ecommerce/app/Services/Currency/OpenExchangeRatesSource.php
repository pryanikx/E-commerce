<?php

declare(strict_types=1);

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class OpenExchangeRatesSource implements CurrencySource
{
    /**
     * @var string $apiKey
     */
    protected string $apiKey;

    /**
     * @var string $apiUrl
     */
    protected string $apiUrl = 'https://openexchangerates.org/api/latest.json';

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->apiKey = config('services.open_exchange_rates.api_key');
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
        $cacheDuration = now()->addHours(24);

        return cache()->remember($cacheKey, $cacheDuration, function () use ($baseCurrency) {
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

                return $data['rates'] ?? [];
            } catch (\Exception $e) {
                $this->logger->error(__('errors.fetch_exchange_rates_failed'), [
                    'message' => $e->getMessage(),
                    'base_currency' => $baseCurrency,
                ]);
                return [
                    'BYN' => 1.0,
                    'USD' => 1.0,
                    'EUR' => 1.0,
                    'RUB' => 1.0,
                ];
            }
        });
    }
}
