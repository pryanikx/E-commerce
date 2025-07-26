<?php

declare(strict_types=1);

namespace App\Services\Currency\Clients;

use App\Exceptions\Currency\CurrencyApiException;
use App\Services\Currency\Clients\Contracts\CurrencyApiClientInterface;
use Illuminate\Http\Client\Factory as HttpFactoryInterface;
use Psr\Log\LoggerInterface;

readonly class OpenExchangeRatesClient implements CurrencyApiClientInterface
{
    /**
     * @param HttpFactoryInterface $http
     * @param LoggerInterface $logger
     * @param string $apiKey
     * @param string $apiUrl
     */
    public function __construct(
        private HttpFactoryInterface $http,
        private LoggerInterface $logger,
        private string $apiKey,
        private string $apiUrl,
    ) {
    }

    /**
     * Fetch raw exchange rates data from the API.
     *
     * @param string $baseCurrency
     * @return array<string, float>
     * @throws CurrencyApiException
     */
    public function fetchRates(string $baseCurrency): array
    {
        try {
            $response = $this->http->get($this->apiUrl, [
                'app_id' => $this->apiKey,
                'base' => $baseCurrency,
            ]);

            if ($response->failed()) {
                $this->logger->error(__('currency.api_request_failed'), [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'base_currency' => $baseCurrency,
                ]);

                throw new CurrencyApiException(
                    __('currency.api_request_failed_status', ['status' => $response->status()]),
                    $response->status()
                );
            }

            $data = $response->json();

            if (!isset($data['rates'])) {
                $this->logger->error(__('currency.invalid_api_response'), [
                    'response_data' => $data,
                    'base_currency' => $baseCurrency,
                ]);

                throw new CurrencyApiException(__('currency.invalid_api_response_missing_rates'));
            }

            $this->logger->info(__('currency.rates_fetched_successfully'), [
                'base_currency' => $baseCurrency,
                'rates_count' => count($data['rates']),
            ]);

            return $data['rates'];
        } catch (CurrencyApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error(__('currency.client_error'), [
                'message' => $e->getMessage(),
                'base_currency' => $baseCurrency,
                'exception_class' => get_class($e),
            ]);

            throw new CurrencyApiException(
                __('currency.fetch_rates_failed', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
