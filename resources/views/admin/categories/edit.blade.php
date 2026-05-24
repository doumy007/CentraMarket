@extends('layouts.admin')

@section('title', 'Editar Categoría')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Editar Categoría</h2>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card card-dash">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">URL de Imagen</label>
                <input type="url" name="image" class="form-control @error('image') is-invalid @enderror" value="{{ old('image', $category->image) }}" placeholder="https://...">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Activo</label>
            </div>
            <button type="submit" class="btn btn-dark">Actualizar</button>
        </form>
    </div>
</div>
@endsection
