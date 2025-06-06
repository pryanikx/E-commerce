<?php

declare(strict_types=1);

namespace App\Services\Currency;

class CurrencyCalculator
{
    private const SUPPORTED_CURRENCIES = ['BYN', 'USD', 'EUR', 'RUB'];
    PRIVATE const BASE_CURRENCY = 'USD';

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
    public function __construct(CurrencySource $source, string $baseCurrency = self::BASE_CURRENCY)
    {
        $this->source = $source;
        $this->baseCurrency = $baseCurrency;
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
            $rate = $rates[$currency] ?? 1.0;
            $converted[$currency] = round($price * $rate, 2);
        }

        return $converted;
    }
}
