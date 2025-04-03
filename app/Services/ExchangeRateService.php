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

    public function get(Currency $from, Currency $to): float
    {
        $cachedRate = $this->exchangeRateCacheService->get($from, $to);
        if ($cachedRate) {
            return $cachedRate;
        }

        $response = Http::timeout(config('exchange.timeout'))->get(config('exchange.endpoint').$from->value);

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

    public function getDefaultValueOf(Currency $from, Currency $to): float
    {
        return config("defaults.{$from->value}.{$to->value}", 0);
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
