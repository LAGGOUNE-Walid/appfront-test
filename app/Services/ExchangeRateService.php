<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    public function __construct(
        private ExchangeRateCacheService $exchangeRateCacheService
    ) {}

    /**
     * Retrieves the exchange rate between two currencies.
     *
     * This method first checks the cache for an existing exchange rate.
     * If the rate is not cached, it fetches the latest rate from the API.
     * If the API request fails, it logs the error and returns a default value.
     * The fetched rate is then cached for future use.
     *
     * @param Currency $from The base currency.
     * @param Currency $to The target currency.
     * @return float The exchange rate from the base currency to the target currency.
     */
    public function get(Currency $from, Currency $to): float
    {
        $cachedRate = $this->exchangeRateCacheService->get($from, $to);
        if ($cachedRate) {
            return $cachedRate;
        }

        $response = Http::timeout(config('exchange.timeout'))->get(config('exchange.endpoint') . $from->value);

        if ($response->failed()) {
            $this->logError($from, $to, $response);

            return $this->getDefaultValueOf($from, $to);
        }

        $rate = $response->json("rates.{$to->value}", null);
        if ($rate === null) {
            return $this->getDefaultValueOf($from, $to);
        }

        return tap($rate, function (float $rate) use ($from, $to) {
            $this->exchangeRateCacheService->set($from, $to, $rate);
        });
    }
    /**
     * Retrieves the default exchange rate between two currencies.
     *
     * This method gets the default exchange rate from the config file
     *
     * @param Currency $from The base currency.
     * @param Currency $to The target currency.
     * @return float The default exchange rate from the base currency to the target currency.
     */
    public function getDefaultValueOf(Currency $from, Currency $to): float
    {
        return config("exchange.defaults.{$from->value}.{$to->value}", 0);
    }

    public function logError(Currency $from, Currency $to, Response $response): void
    {
        Log::error('Exchange rate API request failed', [
            'from' => $from->value,
            'to' => $to->value,
            'status' => $response->status(),
            'error' => $response->body(),
        ]);
    }
}
