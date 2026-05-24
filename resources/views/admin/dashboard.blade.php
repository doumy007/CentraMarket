@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h2>Dashboard</h2>
<p class="text-muted">Bienvenido al panel de administración, {{ auth()->user()->name }}.</p>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-dash">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Productos</p>
                        <p class="stat-number mb-0">{{ $stats['products_count'] }}</p>
                    </div>
                    <i class="bi bi-box fs-1 text-secondary"></i>
                </div>
                <small class="text-success">{{ $stats['active_products'] }} activos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-dash">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Categorías</p>
                        <p class="stat-number mb-0">{{ $stats['categories_count'] }}</p>
                    </div>
                    <i class="bi bi-tags fs-1 text-secondary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-dash">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Órdenes</p>
                        <p class="stat-number mb-0">{{ $stats['orders_count'] }}</p>
                    </div>
                    <i class="bi bi-receipt fs-1 text-secondary"></i>
                </div>
                <small class="text-warning">{{ $stats['pending_orders'] }} pendientes</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-dash">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Usuarios</p>
                        <p class="stat-number mb-0">{{ $stats['users_count'] }}</p>
                    </div>
                    <i class="bi bi-people fs-1 text-secondary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-dash">
    <div class="card-header bg-white">
        <h5 class="mb-0">Últimos Productos</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestProducts as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name }}</td>
                        <td>
                            @if($p->hasActivePromotion())
                                <span class="text-decoration-line-through">${{ number_format($p->price, 0, ',', '.') }}</span>
                                <span class="text-danger">${{ number_format($p->promotion_price, 0, ',', '.') }}</span>
                            @else
                                ${{ number_format($p->price, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>{{ $p->stock }}</td>
                        <td>
                            @if($p->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
