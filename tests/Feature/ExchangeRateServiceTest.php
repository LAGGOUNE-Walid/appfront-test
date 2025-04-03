<?php

namespace Tests\Feature;

use App\Enums\Currency;
use App\Services\ExchangeRateCacheService;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    protected ExchangeRateService $exchangeRateService;

    protected $exchangeRateCacheMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exchangeRateCacheMock = $this->createMock(ExchangeRateCacheService::class);

        $this->exchangeRateService = new ExchangeRateService($this->exchangeRateCacheMock);
    }

    public function test_it_returns_cached_exchange_rate_if_available()
    {
        $this->exchangeRateCacheMock->method('get')->willReturn(1.12);

        $rate = $this->exchangeRateService->get(Currency::DOLLAR, Currency::EURO);

        $this->assertEquals(1.12, $rate);
    }

    public function test_it_fetches_exchange_rate_from_api_if_not_cached()
    {
        $this->exchangeRateCacheMock->method('get')->willReturn(null);

        Http::fake([
            config('exchange.endpoint').'*' => Http::response([
                'rates' => ['EUR' => 1.15],
            ], 200),
        ]);

        $rate = $this->exchangeRateService->get(Currency::DOLLAR, Currency::EURO);

        $this->assertEquals(1.15, $rate);
    }

    public function test_it_returns_default_value_if_api_fails()
    {
        $this->exchangeRateCacheMock->method('get')->willReturn(null);

        Http::fake([
            config('exchange.endpoint').'*' => Http::response([], 500),
        ]);

        Config::set('exchange.defaults.USD.EUR', 1.10);

        $rate = $this->exchangeRateService->get(Currency::DOLLAR, Currency::EURO);

        $this->assertEquals(1.10, $rate);
    }

    public function test_it_logs_error_when_api_request_fails()
    {
        Log::shouldReceive('error')
            ->once();

        $this->exchangeRateCacheMock->method('get')->willReturn(null);

        Http::fake([
            config('exchange.endpoint').'*' => Http::response([], 500),
        ]);

        $this->exchangeRateService->get(Currency::DOLLAR, Currency::EURO);
    }

    public function test_it_caches_the_exchange_rate_after_fetching()
    {
        $this->exchangeRateCacheMock->method('get')->willReturn(null);
        $this->exchangeRateCacheMock->expects($this->once())->method('set')->with(
            Currency::DOLLAR, Currency::EURO, 1.20
        );

        Http::fake([
            config('exchange.endpoint').'*' => Http::response([
                'rates' => ['EUR' => 1.20],
            ], 200),
        ]);

        $rate = $this->exchangeRateService->get(Currency::DOLLAR, Currency::EURO);

        $this->assertEquals(1.20, $rate);
    }
}
