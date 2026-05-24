@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Checkout</h1>

    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="bi bi-truck"></i> Datos de Envío</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo *</label>
                                <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required>
                                @error('shipping_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="shipping_email" class="form-control @error('shipping_email') is-invalid @enderror" value="{{ old('shipping_email', auth()->user()->email ?? '') }}" required>
                                @error('shipping_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">País *</label>
                                <input type="text" name="shipping_country" class="form-control @error('shipping_country') is-invalid @enderror" value="{{ old('shipping_country') }}" required placeholder="Ej: Chile">
                                @error('shipping_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad *</label>
                                <input type="text" name="shipping_city" class="form-control @error('shipping_city') is-invalid @enderror" value="{{ old('shipping_city') }}" required placeholder="Ej: Santiago">
                                @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Calle *</label>
                                <input type="text" name="shipping_street" class="form-control @error('shipping_street') is-invalid @enderror" value="{{ old('shipping_street') }}" required placeholder="Ej: Av. Providencia">
                                @error('shipping_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número</label>
                                <input type="text" name="shipping_number" class="form-control @error('shipping_number') is-invalid @enderror" value="{{ old('shipping_number') }}" placeholder="Ej: 1234">
                                @error('shipping_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="bi bi-credit-card"></i> Método de Pago</h5>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <div>
                                <strong>Flow.cl</strong>
                                <p class="mb-0 text-muted small">Paga con tu tarjeta de crédito o débito (Visa, Mastercard, Amex, etc.) en un entorno 100% seguro.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Resumen del Pedido</h5>
                        @foreach($cart->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="d-block">{{ $item->product->name }}</small>
                                    <small class="text-muted">Cant: {{ $item->quantity }} x ${{ number_format($item->price, 0, ',', '.') }}</small>
                                </div>
                                <span>${{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <hr>
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
                            <strong class="fs-4">${{ number_format($total, 0, ',', '.') }}</strong>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 btn-lg">
                            <i class="bi bi-lock-fill"></i> Pagar ahora
                        </button>

                        <p class="text-center text-muted small mt-3 mb-0">
                            <i class="bi bi-shield-check"></i> Pago 100% seguro. Tus datos están protegidos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
