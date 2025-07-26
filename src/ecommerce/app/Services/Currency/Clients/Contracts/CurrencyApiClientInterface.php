<?php

declare(strict_types=1);

namespace App\Services\Currency\Clients\Contracts;

use App\Exceptions\Currency\CurrencyApiException;

interface CurrencyApiClientInterface
{
    /**
     * Fetch raw exchange rates data from external API.
     *
     * @param string $baseCurrency
     * @return array<string, float>
     * @throws CurrencyApiException
     */
    public function fetchRates(string $baseCurrency): array;
}
