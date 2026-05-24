@extends('layouts.app')

@section('title', 'Orden ' . $order->order_number)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Mis Órdenes</a></li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Detalle de la Orden</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'Producto eliminado' }}</td>
                                    <td>${{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <th>${{ number_format($order->subtotal, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Descuento</th>
                                    <th>${{ number_format($order->discount, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="fs-5">${{ number_format($order->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Resumen</h5>
                    <p class="mb-1"><strong>N° Orden:</strong> {{ $order->order_number }}</p>
                    <p class="mb-1"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mb-1">
                        <strong>Estado:</strong>
                        @switch($order->status)
                            @case('pending') <span class="badge bg-warning text-dark">Pendiente</span> @break
                            @case('paid') <span class="badge bg-success">Pagado</span> @break
                            @case('preparing') <span class="badge bg-info">Preparando</span> @break
                            @case('shipped') <span class="badge bg-primary">Enviado</span> @break
                            @case('delivered') <span class="badge bg-success">Entregado</span> @break
                            @case('cancelled') <span class="badge bg-danger">Cancelado</span> @break
                        @endswitch
                    </p>
                    <p class="mb-0"><strong>Método:</strong> {{ $order->payment_method ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
