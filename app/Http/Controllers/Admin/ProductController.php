<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotion_price' => 'nullable|numeric|min:0',
            'promotion_active' => 'boolean',
            'promotion_start' => 'nullable|date',
            'promotion_end' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];

        if ($request->filled('promotion_start') && $request->filled('promotion_end')) {
            $rules['promotion_end'] = 'nullable|date|after:promotion_start';
        }

        $data = $request->validate($rules);

        $slug = Str::slug($data['name']);
        $original = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }
        $data['slug'] = $slug;
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
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotion_price' => 'nullable|numeric|min:0',
            'promotion_active' => 'boolean',
            'promotion_start' => 'nullable|date',
            'promotion_end' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];

        if ($request->filled('promotion_start') && $request->filled('promotion_end')) {
            $rules['promotion_end'] = 'nullable|date|after:promotion_start';
        }

        $data = $request->validate($rules);

        if ($product->name !== $data['name']) {
            $slug = Str::slug($data['name']);
            $original = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $original . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

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

    public function exportExcel()
    {
        $products = Product::with('category')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['nombre', 'categoria', 'descripcion', 'precio', 'precio_promocional', 'promocion_activa', 'inicio_promocion', 'fin_promocion', 'stock', 'activo', 'imagen'];
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col++, 1, $h);
        }

        $row = 2;
        foreach ($products as $p) {
            $sheet->setCellValueByColumnAndRow(1, $row, $p->name);
            $sheet->setCellValueByColumnAndRow(2, $row, $p->category->name);
            $sheet->setCellValueByColumnAndRow(3, $row, $p->description);
            $sheet->setCellValueByColumnAndRow(4, $row, $p->price);
            $sheet->setCellValueByColumnAndRow(5, $row, $p->promotion_price);
            $sheet->setCellValueByColumnAndRow(6, $row, $p->promotion_active ? 1 : 0);
            $sheet->setCellValueByColumnAndRow(7, $row, $p->promotion_start?->format('Y-m-d H:i'));
            $sheet->setCellValueByColumnAndRow(8, $row, $p->promotion_end?->format('Y-m-d H:i'));
            $sheet->setCellValueByColumnAndRow(9, $row, $p->stock);
            $sheet->setCellValueByColumnAndRow(10, $row, $p->is_active ? 1 : 0);
            $sheet->setCellValueByColumnAndRow(11, $row, $p->image);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="productos.xlsx"');

        return $response;
    }

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return back()->with('error', 'El archivo no contiene datos.');
        }

        $headers = array_map('strtolower', $rows[0]);
        $created = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $data = array_combine($headers, $rows[$i]);

            $name = trim($data['nombre'] ?? '');
            if (empty($name)) {
                $errors[] = "Fila " . ($i + 1) . ": nombre vacío";
                continue;
            }

            $categoryName = trim($data['categoria'] ?? '');
            if (empty($categoryName)) {
                $errors[] = "Fila " . ($i + 1) . " ($name): categoría vacía";
                continue;
            }

            $category = Category::where('name', $categoryName)->first();
            if (!$category) {
                $errors[] = "Fila " . ($i + 1) . " ($name): categoría '$categoryName' no encontrada";
                continue;
            }

            $price = floatval($data['precio'] ?? 0);
            if ($price <= 0) {
                $errors[] = "Fila " . ($i + 1) . " ($name): precio inválido";
                continue;
            }

            $slug = Str::slug($name);
            $original = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $counter++;
            }

            $promoActive = in_array(trim($data['promocion_activa'] ?? '0'), ['1', 'yes', 'si', 'true']);

            $promoStart = null;
            if (!empty($data['inicio_promocion'])) {
                try { $promoStart = \Carbon\Carbon::parse($data['inicio_promocion']); } catch (\Exception $e) {}
            }

            $promoEnd = null;
            if (!empty($data['fin_promocion'])) {
                try { $promoEnd = \Carbon\Carbon::parse($data['fin_promocion']); } catch (\Exception $e) {}
            }

            $isActive = in_array(trim($data['activo'] ?? '1'), ['1', 'yes', 'si', 'true', '']);

            $imageFilename = trim($data['imagen'] ?? '');
            if (!empty($imageFilename)) {
                $imagePath = base_path('imgProduct/' . $imageFilename);
                if (!file_exists($imagePath)) {
                    $errors[] = "Fila " . ($i + 1) . " ($name): imagen '$imageFilename' no encontrada en imgProduct/";
                    $imageFilename = null;
                }
            } else {
                $imageFilename = null;
            }

            try {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => trim($data['descripcion'] ?? ''),
                    'price' => $price,
                    'promotion_price' => !empty($data['precio_promocional']) ? floatval($data['precio_promocional']) : null,
                    'promotion_active' => $promoActive,
                    'promotion_start' => $promoStart,
                    'promotion_end' => $promoEnd,
                    'stock' => intval($data['stock'] ?? 0),
                    'is_active' => $isActive,
                    'image' => $imageFilename,
                ]);

                if ($imageFilename) {
                    $product->images()->create([
                        'url' => $imageFilename,
                        'order' => 1,
                    ]);
                }

                $created++;
            } catch (\Exception $e) {
                $errors[] = "Fila " . ($i + 1) . " ($name): " . $e->getMessage();
            }
        }

        $message = "$created producto(s) importado(s) exitosamente.";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' error(es): ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.products.import.form')
            ->with('success', $message);
    }
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
