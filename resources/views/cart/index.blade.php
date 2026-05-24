@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Carrito de Compras</h1>

    @if(!$cart || $cart->items->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <p class="mt-3 fs-5 text-muted">Tu carrito está vacío.</p>
            <a href="{{ route('products.index') }}" class="btn btn-dark">Ver Productos</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                @php $subtotal = 0; @endphp
                @foreach($cart->items as $item)
                    @php
                        $itemSubtotal = $item->price * $item->quantity;
                        $subtotal += $itemSubtotal;
                    @endphp
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="{{ $item->product->image ?? 'https://picsum.photos/seed/default/200/200' }}" class="img-fluid rounded" style="height:100px;width:100px;object-fit:cover;">
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <small class="text-muted">{{ $item->product->category->name }}</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="fw-bold">${{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="d-flex align-items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="this.nextElementSibling.stepDown(); this.form.submit();">-</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center" style="width:50px;" onchange="this.form.submit();">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="this.previousElementSibling.stepUp(); this.form.submit();">+</button>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <span class="fw-bold">${{ number_format($itemSubtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-md-1 text-end">
                                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Resumen</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="fs-5">${{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>

                        @auth
                            <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 btn-lg">
                                Proceder al Pago <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 btn-lg">
                                Proceder al Pago <i class="bi bi-arrow-right"></i>
                            </a>
                        @endauth

                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
