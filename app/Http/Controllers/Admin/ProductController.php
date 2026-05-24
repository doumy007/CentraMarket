<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['promotion_active'] = $request->boolean('promotion_active', false);
        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        $this->handleImages($request, $product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $product->load('images');
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
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['promotion_active'] = $request->boolean('promotion_active', false);
        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        $this->handleImages($request, $product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            $this->deleteImageFile($image->url);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        $this->deleteImageFile($image->url);
        $wasPrimary = $product->image === $image->url;
        $image->delete();

        if ($wasPrimary) {
            $firstRemaining = $product->images()->orderBy('order')->first();
            $product->update(['image' => $firstRemaining?->url]);
        }

        return back()->with('success', 'Imagen eliminada.');
    }

    private function handleImages(Request $request, Product $product): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $maxOrder = $product->images()->max('order') ?? 0;
        $isFirst = $product->images()->count() === 0 && !$product->image;

        foreach ($request->file('images') as $file) {
            $maxOrder++;
            $filename = time() . '_' . Str::random(12) . '.' . $file->extension();
            $file->move(base_path('imgProduct'), $filename);

            $product->images()->create([
                'url' => $filename,
                'order' => $maxOrder,
            ]);

            if ($isFirst) {
                $product->update(['image' => $filename]);
                $isFirst = false;
            }
        }
    }

    private function deleteImageFile(?string $filename): void
    {
        if (!$filename) return;
        $path = base_path('imgProduct/' . $filename);
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
