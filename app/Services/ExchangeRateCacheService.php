<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ExchangeRateCacheService
{
    private function getCacheKey(Currency $from, Currency $to): string
    {
        return Str::replaceArray('?', [$from->value, $to->value], config('exchange.cache.key'));
    }

    public function get(Currency $from, Currency $to): ?float
    {
        return Cache::get($this->getCacheKey($from, $to), null);
    }

    public function set(Currency $from, Currency $to, float $value): void
    {
        Cache::set($this->getCacheKey($from, $to), $value, config('exchange.cache.ttl'));
    }
}
