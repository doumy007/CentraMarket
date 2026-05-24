@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    .product-gallery { position: relative; }
    .product-gallery .main-img { width: 100%; height: 500px; object-fit: cover; border-radius: .75rem; }
    .product-gallery .thumb-img { width: 80px; height: 80px; object-fit: cover; border-radius: .5rem; cursor: pointer; border: 2px solid transparent; transition: all .2s; }
    .product-gallery .thumb-img:hover, .product-gallery .thumb-img.active { border-color: #212529; }
    .promo-badge { position: absolute; top: 1rem; left: 1rem; background: #dc3545; color: white; padding: .35rem .85rem; border-radius: 2rem; font-weight: 700; font-size: .9rem; z-index: 2; }
    .original-price { text-decoration: line-through; color: #adb5bd; font-size: 1.2rem; }
    .promotion-price { font-size: 2.2rem; font-weight: 800; color: #dc3545; }
    .normal-price { font-size: 2.2rem; font-weight: 800; color: #212529; }
    .btn-buy { background: #dc3545; color: white; padding: .8rem 2rem; font-size: 1.15rem; font-weight: 700; border: none; border-radius: .5rem; transition: all .2s; }
    .btn-buy:hover { background: #bb2d3b; color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,53,69,.35); }
    .btn-cart { padding: .8rem 1.5rem; font-size: 1.15rem; border-radius: .5rem; }
    .feature-icon { width: 48px; height: 48px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #212529; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Productos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="product-gallery">
                @if($product->hasActivePromotion())
                    <span class="promo-badge">{{ $product->promotionPercentage() }}% OFF</span>
                @endif
                <img id="mainImage" src="{{ $product->image ?? 'https://picsum.photos/seed/default/800/800' }}" class="main-img w-100" alt="{{ $product->name }}">

                @php
                    $allImages = collect([$product->image]);
                    if ($product->images->isNotEmpty()) {
                        $allImages = $product->images->pluck('url')->prepend($product->image);
                    }
                    $allImages = $allImages->filter()->unique()->take(5);
                @endphp

                @if($allImages->count() > 1)
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($allImages as $idx => $img)
                            <img src="{{ $img }}" class="thumb-img {{ $idx === 0 ? 'active' : '' }}" onclick="document.getElementById('mainImage').src=this.src; document.querySelectorAll('.thumb-img').forEach(el=>el.classList.remove('active')); this.classList.add('active');">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <h1 class="fw-bold mb-2">{{ $product->name }}</h1>
            <p class="text-muted mb-3">
                <i class="bi bi-tag"></i> {{ $product->category->name }}
                <span class="mx-2">|</span>
                <i class="bi bi-box"></i> SKU: {{ $product->slug }}
            </p>

            <div class="mb-4">
                @if($product->hasActivePromotion())
                    <span class="original-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="promotion-price ms-2">${{ number_format($product->promotion_price, 0, ',', '.') }}</span>
                    <span class="badge bg-danger ms-2" style="font-size:.8rem;">-{{ $product->promotionPercentage() }}%</span>
                @else
                    <span class="normal-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
            </div>

            <div class="mb-4 d-flex align-items-center gap-2">
                <strong>Stock:</strong>
                @if($product->stock > 10)
                    <span class="badge bg-success fs-6">{{ $product->stock }} disponibles</span>
                @elseif($product->stock > 0)
                    <span class="badge bg-warning text-dark fs-6">Solo {{ $product->stock }} restantes</span>
                @else
                    <span class="badge bg-danger fs-6">Agotado</span>
                @endif
            </div>

            <p class="lead mb-4">{{ $product->description }}</p>

            @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <div class="input-group" style="width:130px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="this.nextElementSibling.stepDown();">-</button>
                                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                                <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.stepUp();">+</button>
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-cart btn-outline-dark w-100">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-buy w-100">
                        <i class="bi bi-lightning-fill"></i> Comprar Ahora
                    </button>
                </form>
            @endif

            <hr>

            <div class="row g-3">
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-truck"></i></div>
                        <div><small class="text-muted d-block">Envío</small><strong>Rápido y Seguro</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <div><small class="text-muted d-block">Pago</small><strong>100% Seguro</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <div><small class="text-muted d-block">Cambios</small><strong>30 Días</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-headset"></i></div>
                        <div><small class="text-muted d-block">Soporte</small><strong>24/7</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <h3 class="mt-5 mb-4 fw-bold">Productos Relacionados</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
                <div class="col-sm-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($related->hasActivePromotion())
                                <span class="promo-badge" style="left:.5rem;top:.5rem;font-size:.75rem;padding:.2rem .6rem;">-{{ $related->promotionPercentage() }}%</span>
                            @endif
                            <img src="{{ $related->image ?? 'https://picsum.photos/seed/default/600/600' }}" class="card-img-top" alt="{{ $related->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $related->name }}</h6>
                            <div class="mt-auto">
                                @if($related->hasActivePromotion())
                                    <small class="text-decoration-line-through text-muted">${{ number_format($related->price, 0, ',', '.') }}</small>
                                    <span class="price-tag">${{ number_format($related->promotion_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-tag">${{ number_format($related->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $related->slug) }}" class="btn btn-outline-dark btn-sm mt-2">Ver</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
