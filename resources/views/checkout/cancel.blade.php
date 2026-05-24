@extends('layouts.app')

@section('title', 'Pago Cancelado')

@section('content')
<div class="container py-5 text-center">
    <div class="mb-4">
        <i class="bi bi-x-circle-fill text-danger" style="font-size:5rem;"></i>
    </div>
    <h1 class="fw-bold mb-3">Pago Cancelado</h1>
    <p class="lead text-muted mb-4">El proceso de pago fue cancelado. Si tienes dudas, contáctanos.</p>

    <div class="mt-4">
        <a href="{{ route('cart.index') }}" class="btn btn-dark">Volver al Carrito</a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark ms-2">Seguir Comprando</a>
    </div>
</div>
@endsection
