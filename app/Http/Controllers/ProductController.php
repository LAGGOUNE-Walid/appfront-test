<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Http\Requests\CreateOrUpdateProductRequest;
use App\Models\Product;
use App\Services\ExchangeRateService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private ProductService $productService
    ) {}

    public function index(): View
    {
        return view('products.list', [
            'products' => Product::paginate(12),
            'exchangeRate' => $this->exchangeRateService->get(from: Currency::DOLLAR, to: Currency::EURO),
        ]);
    }

    public function show(int $product_id): View
    {
        return view('products.show', [
            'product' => Product::cachedFindOrFail($product_id),
            'exchangeRate' => $this->exchangeRateService->get(from: Currency::DOLLAR, to: Currency::EURO),
        ]);
    }

    public function create(CreateOrUpdateProductRequest $request): RedirectResponse
    {
        $this->productService->create(
            $request->name,
            (float) $request->price,
            $request->description,
            $request->hasFile('image') ? $request->file('image') : null
        );

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    public function update(CreateOrUpdateProductRequest $request, int $id): RedirectResponse
    {
        $this->productService->update(
            Product::findOrFail($id),
            $request->name,
            (float) $request->price,
            $request->description,
            $request->hasFile('image') ? $request->file('image') : null
        );

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function delete(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}
