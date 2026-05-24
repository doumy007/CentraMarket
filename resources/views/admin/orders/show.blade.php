@extends('layouts.admin')

@section('title', 'Orden ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Orden #{{ $order->order_number }}</h2>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-dash mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Items de la Orden</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
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
        <div class="card card-dash mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Información</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Usuario:</strong> {{ $order->user->name ?? 'Invitado' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $order->user->email ?? '-' }}</p>
                <p class="mb-1"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p class="mb-1"><strong>Método:</strong> {{ $order->payment_method ?? '-' }}</p>
                <p class="mb-1"><strong>Transacción:</strong> {{ $order->transaction_id ?? '-' }}</p>
                <p class="mb-0"><strong>Notas:</strong> {{ $order->notes ?? 'Sin notas' }}</p>
            </div>
        </div>

        <div class="card card-dash">
            <div class="card-header bg-white">
                <h5 class="mb-0">Actualizar Estado</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Pagado</option>
                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparando</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Enviado</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Actualizar Estado</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
