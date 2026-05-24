@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Categorías</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-lg"></i> Nueva Categoría
    </a>
</div>

<div class="card card-dash">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->products_count }}</td>
                        <td>
                            @if($cat->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
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
                        <td colspan="5" class="text-center py-4 text-muted">No hay categorías.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $categories->links() }}
</div>
@endsection
