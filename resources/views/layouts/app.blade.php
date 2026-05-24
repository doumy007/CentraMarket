<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda Collahuasi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; }
        .product-card { transition: transform .2s; border: none; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        .product-card .card-img-top { height: 220px; object-fit: cover; }
        .footer { background-color: #212529; color: #adb5bd; padding: 2rem 0; margin-top: 3rem; }
        .price-tag { font-size: 1.35rem; font-weight: 700; color: #dc3545; }
        .filter-section { background: white; border-radius: .5rem; padding: 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 1.5rem; }
        .btn-admin { background-color: #212529; color: white; }
        .btn-admin:hover { background-color: #000; color: white; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shop"></i> Centra Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('orders.lookup') }}">
                            <i class="bi bi-search"></i> Buscar Orden
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-gear"></i> Admin
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}">
                                <i class="bi bi-receipt"></i> Mis Órdenes
                            </a>
                        </li>
                    @endauth
                </ul>
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @php
                                $cartCount = 0;
                                if (auth()->check()) {
                                    $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
                                    $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                                } else {
                                    $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
                                    $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                                }
                            @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Centra Market. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
