<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(public ProductService $productService) {}

    public function loginPage()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->except('_token'))) {
            return redirect()->route('admin.products');
        }

        return redirect()->back()->with('error', 'Invalid login credentials');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function products()
    {
        $products = Product::paginate(12);

        return view('admin.products', compact('products'));
    }

    public function editProduct($id)
    {
        // no need for cache , admins will not apply load to this page
        $product = Product::findOrFail($id);

        return view('admin.edit_product', compact('product'));
    }

    public function addProductForm()
    {
        return view('admin.add_product');
    }
}
