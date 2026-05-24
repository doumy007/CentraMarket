@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4 fw-bold">Centra Market</h1>
    <p class="lead text-muted">Los mejores productos al mejor precio</p>
    <a href="{{ route('products.index') }}" class="btn btn-dark btn-lg mt-3">
        Ver Productos <i class="bi bi-arrow-right"></i>
    </a>
</div>

@php
    $featuredProducts = App\Models\Product::where('is_active', true)->with('category')->latest()->take(8)->get();
@endphp

@if($featuredProducts->isNotEmpty())
<div class="container">
    <h2 class="mb-4">Productos Destacados</h2>
    <div class="row g-4">
        @foreach($featuredProducts as $product)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <img src="{{ $product->image ?? 'https://picsum.photos/seed/default/600/600' }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text small text-muted mb-2">{{ $product->category->name }}</p>
                        <p class="price-tag mt-auto">${{ number_format($product->price, 0, ',', '.') }}</p>
                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-dark w-100">Ver Detalle</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
