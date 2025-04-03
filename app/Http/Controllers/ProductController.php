<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ExchangeRateService $exchangeRateService
    ) {}

    public function index() : View
    {
        return view('products.list', [
            "products" => Product::paginate(12),
            "exchangeRate" => $this->exchangeRateService->get(from: Currency::DOLLAR, to: Currency::EURO)
        ]);
    }

    public function show(Request $request): View
    {
        return view('products.show', [
            "product" => Product::cachedFindOrFail($request->route('product_id')),
            "exchangeRate" => $this->exchangeRateService->get(from: Currency::DOLLAR, to: Currency::EURO)
        ]);
    }
}
