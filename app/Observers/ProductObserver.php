<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        Cache::put("product:{$product->id}", $product, now()->addMinutes(10));
    }

    public function deleted(Product $product): void
    {
        Cache::forget("product:{$product->id}", $product, now()->addMinutes(10));
    }

    public function updated(Product $product): void
    {
        Cache::put("product:{$product->id}", $product, now()->addMinutes(10));
    }
}
