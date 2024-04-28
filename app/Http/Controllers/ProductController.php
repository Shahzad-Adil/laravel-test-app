<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('products.index');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }
}
