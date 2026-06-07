@extends('layouts.admin')

@section('title', 'Importar Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Importar / Exportar Productos</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.export') }}" class="btn btn-primary-custom">
            <i class="bi bi-download"></i> Exportar Excel
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card card-dash h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-upload"></i> Importar Productos</h5>
                <p class="text-muted small">Sube un archivo Excel (.xlsx, .xls, .csv) con las siguientes columnas:</p>

                <div class="table-responsive small mb-3">
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Columna</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>nombre</td><td>Sí</td><td>Nombre del producto</td></tr>
                            <tr><td>categoria</td><td>Sí</td><td>Nombre de la categoría (debe existir)</td></tr>
                            <tr><td>descripcion</td><td>No</td><td>Descripción del producto</td></tr>
                            <tr><td>precio</td><td>Sí</td><td>Precio normal</td></tr>
                            <tr><td>precio_promocional</td><td>No</td><td>Precio con descuento</td></tr>
                            <tr><td>promocion_activa</td><td>No</td><td>1, 0, yes, no</td></tr>
                            <tr><td>inicio_promocion</td><td>No</td><td>YYYY-MM-DD HH:MM</td></tr>
                            <tr><td>fin_promocion</td><td>No</td><td>YYYY-MM-DD HH:MM</td></tr>
                            <tr><td>stock</td><td>Sí</td><td>Cantidad disponible</td></tr>
                            <tr><td>activo</td><td>No</td><td>1, 0, yes, no</td></tr>
                            <tr><td>imagen</td><td>No</td><td>Nombre del archivo en <code>imgProduct/</code></td></tr>
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Seleccionar archivo</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-dash h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-download"></i> Exportar Productos</h5>
                <p class="text-muted small">Descarga todos los productos en un archivo Excel con todas las columnas disponibles.</p>
                <p class="text-muted small">Puedes usar este archivo como plantilla: modifica los datos y luego impórtalo de vuelta.</p>
                <a href="{{ route('admin.products.export') }}" class="btn btn-primary-custom">
                    <i class="bi bi-download"></i> Descargar Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection