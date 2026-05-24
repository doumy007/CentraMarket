<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotion_price' => 'nullable|numeric|min:0|lt:price',
            'promotion_active' => 'boolean',
            'promotion_start' => 'nullable|date',
            'promotion_end' => 'nullable|date|after:promotion_start',
            'image' => 'nullable|url',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['promotion_active'] = $request->boolean('promotion_active', false);
        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotion_price' => 'nullable|numeric|min:0|lt:price',
            'promotion_active' => 'boolean',
            'promotion_start' => 'nullable|date',
            'promotion_end' => 'nullable|date|after:promotion_start',
            'image' => 'nullable|url',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['promotion_active'] = $request->boolean('promotion_active', false);
        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
