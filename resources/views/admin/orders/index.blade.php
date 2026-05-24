@extends('layouts.admin')

@section('title', 'Órdenes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Órdenes</h2>
</div>

<div class="card card-dash">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>N° Orden</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->user->name ?? 'Invitado' }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>${{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>{{ $order->payment_method ?? '-' }}</td>
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
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No hay órdenes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $orders->links() }}
</div>
@endsection
