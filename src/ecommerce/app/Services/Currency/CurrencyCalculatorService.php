<?php

declare(strict_types=1);

namespace App\Services\Currency;

class CurrencyCalculatorService
{
    private const DEFAULT_RATE = 1.0;

    private const PRECISION = 2;

    /**
     * @param CurrencySource $source
     * @param string $baseCurrency
     * @param array<string> $supportedCurrencies
     */
    public function __construct(
        private readonly CurrencySource $source,
        private readonly string $baseCurrency,
        private readonly array $supportedCurrencies,
    ) {
    }

    /**
     * Convert a price to multiple currencies.
     *
     * @param float $price
     * @param array<string>|null $targetCurrencies
     *
     * @return array<string, float>
     */
    public function convert(float $price, ?array $targetCurrencies = null): array
    {
        $targetCurrencies ??= $this->supportedCurrencies;
        $rates = $this->source->getExchangeRates($this->baseCurrency);
        $converted = [];

        foreach ($targetCurrencies as $currency) {
            $rate = $rates[$currency] ?? self::DEFAULT_RATE;
            $converted[$currency] = round($price * $rate, self::PRECISION);
        }

        return $converted;
    }
}
