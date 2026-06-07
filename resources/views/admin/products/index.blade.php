@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Productos</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.import.form') }}" class="btn btn-outline-dark">
            <i class="bi bi-upload"></i> Importar/Exportar
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-lg"></i> Nuevo Producto
        </a>
    </div>
</div>

<div class="card card-dash">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Normal</th>
                        <th>Promo</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>
                            @if($p->image)
                                <img src="{{ url('imgProduct/' . $p->image) }}" style="width:50px;height:50px;object-fit:cover;border-radius:.25rem;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name }}</td>
                        <td>${{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>
                            @if($p->hasActivePromotion())
                                <span class="badge bg-danger">${{ number_format($p->promotion_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
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
                        <td>
                            <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay productos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $products->links() }}
</div>
@endsection
