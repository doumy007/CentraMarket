<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Centra Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f1f3f5; }
        .sidebar { background-color: #212529; min-height: 100vh; padding-top: 1rem; }
        .sidebar .nav-link { color: #adb5bd; padding: .6rem 1rem; border-radius: .25rem; margin: .15rem .5rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background-color: #343a40; }
        .sidebar .nav-link i { margin-right: .5rem; }
        .content-area { padding: 1.5rem; }
        .card-dash { border: none; box-shadow: 0 1px 6px rgba(0,0,0,.06); }
        .card-dash .card-body { padding: 1.5rem; }
        .stat-number { font-size: 2rem; font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar d-none d-md-block p-0">
                <div class="px-3 mb-3">
                    <a href="/" class="text-white text-decoration-none fw-bold fs-5">
                        <i class="bi bi-shop"></i> Tienda
                    </a>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags"></i> Categorías
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box"></i> Productos
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-receipt"></i> Órdenes
                    </a>
                    <hr class="text-secondary mx-2">
                    <a class="nav-link" href="/">
                        <i class="bi bi-arrow-left"></i> Volver a Tienda
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-start w-100 text-decoration-none" style="color:#adb5bd;">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </button>
                    </form>
                </nav>
            </div>

            <div class="col-md-10 ms-auto content-area">
                <div class="d-md-none mb-3">
                    <nav class="navbar navbar-dark bg-dark rounded">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="/"><i class="bi bi-shop"></i> Admin</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminMobileNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="adminMobileNav">
                                <ul class="navbar-nav">
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Categorías</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Productos</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Órdenes</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/">Volver a Tienda</a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
