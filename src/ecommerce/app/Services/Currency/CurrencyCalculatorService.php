<?php

declare(strict_types=1);

namespace App\Services\Currency;

class CurrencyCalculatorService
{
    private const DEFAULT_RATE = 1.0;

    private const PRECISION = 2;

    /**
     * @var CurrencySource $source
     */
    protected CurrencySource $source;

    /**
     * @var string $baseCurrency
     */
    protected string $baseCurrency;

    /**
     * @param CurrencySource $source
     * @param string $baseCurrency
     */
    public function __construct(CurrencySource $source, string $baseCurrency = null)
    {
        $this->source = $source;
        $this->baseCurrency = $baseCurrency ?? config('services.open_exchange_rates.base_currency', 'USD');
    }

    /**
     * Convert a price to multiple currencies.
     *
     * @param float $price
     * @param array<string> $targetCurrencies
     * @return array<string, float>
     */
    public function convert(float $price, array $targetCurrencies = self::SUPPORTED_CURRENCIES): array
    {
        $rates = $this->source->getExchangeRates($this->baseCurrency);
        $converted = [];

        foreach ($targetCurrencies as $currency) {
            $rate = $rates[$currency] ?? self::DEFAULT_RATE;
            $converted[$currency] = round($price * $rate, self::PRECISION);
        }

        return $converted;
    }
}
