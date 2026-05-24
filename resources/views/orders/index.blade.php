@extends('layouts.app')

@section('title', 'Mis Órdenes')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Mis Órdenes</h1>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-receipt display-1 text-muted"></i>
            <p class="mt-3 fs-5 text-muted">No tienes órdenes aún.</p>
            <a href="{{ route('products.index') }}" class="btn btn-dark">Ir a Comprar</a>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Orden</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @switch($order->status)
                                    @case('pending') <span class="badge bg-warning text-dark">Pendiente</span> @break
                                    @case('paid') <span class="badge bg-success">Pagado</span> @break
                                    @case('preparing') <span class="badge bg-info">Preparando</span> @break
                                    @case('shipped') <span class="badge bg-primary">Enviado</span> @break
                                    @case('delivered') <span class="badge bg-success">Entregado</span> @break
                                    @case('cancelled') <span class="badge bg-danger">Cancelado</span> @break
                                @endswitch
                            </td>
                            <td>{{ $order->payment_method ?? '-' }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark">Detalle</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
