<?php

declare(strict_types=1);

namespace App\Services\Currency;

interface CurrencySource
{
    /**
     * Fetch exchange rates with base currency.
     *
     * @param string $baseCurrency
     * @return array<string, float>
     */
    public function getExchangeRates(string $baseCurrency): array;
}
