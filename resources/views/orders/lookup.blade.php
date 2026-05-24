@extends('layouts.app')

@section('title', 'Buscar Orden')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
                    <h3 class="mb-3">Buscar tu Orden</h3>
                    <p class="text-muted mb-4">Ingresa el email que usaste al comprar para ver tus órdenes.</p>

                    <form method="POST" action="{{ route('orders.search') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="tu@email.com" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-dark btn-lg w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
