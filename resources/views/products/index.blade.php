@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Nuestros Productos</h1>
        @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar Filtros</a>
        @endif
    </div>

    <div class="filter-section">
        <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Buscar producto..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio Mín</label>
                <input type="number" name="min_price" class="form-control" placeholder="$0" value="{{ request('min_price') }}" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio Máx</label>
                <input type="number" name="max_price" class="form-control" placeholder="$9999" value="{{ request('max_price') }}" step="0.01">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-emoji-frown display-1 text-muted"></i>
            <p class="mt-3 fs-5 text-muted">No se encontraron productos.</p>
            <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Limpiar Filtros</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($product->hasActivePromotion())
                                <span class="promo-badge" style="left:.5rem;top:.5rem;font-size:.75rem;padding:.2rem .6rem;">-{{ $product->promotionPercentage() }}%</span>
                            @endif
                            <img src="{{ $product->image ? url('imgProduct/' . $product->image) : 'https://picsum.photos/seed/default/600/600' }}" class="card-img-top" alt="{{ $product->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text small text-muted mb-2">{{ $product->category->name }}</p>
                            <div class="mt-auto">
                                @if($product->hasActivePromotion())
                                    <small class="text-decoration-line-through text-muted">${{ number_format($product->price, 0, ',', '.') }}</small>
                                    <span class="price-tag ms-1">${{ number_format($product->promotion_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-tag">${{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-dark w-100 mt-2">Ver Detalle</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
