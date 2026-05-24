@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Editar Producto</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card card-dash">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Precio Normal ($)</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" step="0.01" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nuevas Imágenes</label>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="form-control @error('images.*') is-invalid @enderror">
                    <small class="text-muted">Agrega más imágenes. La primera de todas será la principal.</small>
                    @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @if($product->images->isNotEmpty())
            <div class="mb-3">
                <label class="form-label">Galería actual</label>
                <div class="d-flex gap-3 flex-wrap">
                    @foreach($product->images as $img)
                        <div class="position-relative" style="width:120px;">
                            <img src="{{ url('imgProduct/' . $img->url) }}" class="img-thumbnail d-block" style="width:120px;height:120px;object-fit:cover;">
                            <div class="mt-1 d-flex justify-content-between align-items-center">
                                @if($product->image === $img->url)
                                    <span class="badge bg-dark" style="font-size:.65rem;">Principal</span>
                                @else
                                    <span></span>
                                @endif
                                <form action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="POST" onsubmit="return confirm('¿Eliminar esta imagen?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="line-height:1;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $product->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>
            <h5 class="mb-3">Promoción</h5>
            <div class="row">
                <div class="col-md-3 mb-3 form-check">
                    <input type="checkbox" name="promotion_active" class="form-check-input" id="promotion_active" value="1" {{ $product->promotion_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="promotion_active">Activar Promoción</label>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Precio Promocional ($)</label>
                    <input type="number" name="promotion_price" class="form-control @error('promotion_price') is-invalid @enderror" value="{{ old('promotion_price', $product->promotion_price) }}" step="0.01">
                    @error('promotion_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="datetime-local" name="promotion_start" class="form-control @error('promotion_start') is-invalid @enderror" value="{{ old('promotion_start', $product->promotion_start?->format('Y-m-d\TH:i')) }}">
                    @error('promotion_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="datetime-local" name="promotion_end" class="form-control @error('promotion_end') is-invalid @enderror" value="{{ old('promotion_end', $product->promotion_end?->format('Y-m-d\TH:i')) }}">
                    @error('promotion_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Activo</label>
            </div>

            <button type="submit" class="btn btn-dark">Actualizar</button>
        </form>
    </div>
</div>
@endsection
