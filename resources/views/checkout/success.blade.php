@extends('layouts.app')

@section('title', 'Pago Exitoso')

@if($order)
@push('pixel_events')
<script>
fbq('track', 'Purchase', {
    value: {{ $order->total }},
    currency: 'CLP',
    order_number: '{{ $order->order_number }}',
    contents: [
        @foreach($order->items as $item)
        { id: '{{ $item->product_id }}', quantity: {{ $item->quantity }}, price: {{ $item->price }} },
        @endforeach
    ]
});
</script>
@endpush
@endif

@section('content')
<div class="container py-5 text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
    </div>
    <h1 class="fw-bold mb-3">¡Pago Exitoso!</h1>
    <p class="lead text-muted mb-4">Gracias por tu compra. Tu pedido ha sido procesado correctamente.</p>

    @if($order)
        <div class="card border-0 shadow-sm mx-auto" style="max-width:500px;">
            <div class="card-body text-start">
                <p class="mb-1"><strong>N° Orden:</strong> {{ $order->order_number }}</p>
                <p class="mb-1"><strong>Total:</strong> ${{ number_format($order->total, 0, ',', '.') }}</p>
                <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-success">Pagado</span></p>
                <p class="mb-1"><strong>Nombre:</strong> {{ $order->shipping_name }}</p>
                <p class="mb-1"><strong>Dirección:</strong> {{ $order->shipping_street }} {{ $order->shipping_number }}, {{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
                <p class="mb-0"><strong>Email:</strong> {{ $order->shipping_email }}</p>
            </div>
        </div>

        <div class="mt-4 alert alert-info d-inline-block">
            <i class="bi bi-info-circle"></i>
            Guarda tu número de orden: <strong>{{ $order->order_number }}</strong>.
            Puedes <a href="{{ route('orders.lookup') }}" class="alert-link">buscar tu orden aquí</a> para ver su estado.
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-dark">Seguir Comprando</a>
    </div>
</div>
@endsection
