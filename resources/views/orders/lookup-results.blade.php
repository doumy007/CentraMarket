@extends('layouts.app')

@section('title', 'Resultados de Búsqueda')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.lookup') }}" class="text-decoration-none">Buscar Orden</a></li>
            <li class="breadcrumb-item active">Resultados</li>
        </ol>
    </nav>

    <h1 class="mb-1">Órdenes de <strong>{{ $data['email'] }}</strong></h1>
    <p class="text-muted mb-4">Últimas órdenes asociadas a este email.</p>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="mt-3 fs-5 text-muted">No se encontraron órdenes para este email.</p>
            <a href="{{ route('orders.lookup') }}" class="btn btn-outline-dark">Intentar con otro email</a>
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
                            <th>Dirección Envío</th>
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
                            <td>
                                <small>{{ $order->shipping_street }} {{ $order->shipping_number }}, {{ $order->shipping_city }}, {{ $order->shipping_country }}</small>
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
