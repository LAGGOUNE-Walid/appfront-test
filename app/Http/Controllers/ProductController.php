<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ExchangeRateService $exchangeRateService
    ) {}

    public function index()
    {
        $products = Product::paginate(12);

        $exchangeRate = $this->exchangeRateService->get(from: Currency::DOLLAR, to: Currency::EURO);

        return view('products.list', compact('products', 'exchangeRate'));
    }

    public function show(Request $request)
    {
        $id = $request->route('product_id');
        $product = Product::find($id);
        $exchangeRate = $this->getExchangeRate();

        return view('products.show', compact('product', 'exchangeRate'));
    }
}
