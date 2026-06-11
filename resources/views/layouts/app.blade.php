<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda Collahuasi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --p-dark: #1a1a2e;
            --p-red: #e63946;
            --p-orange: #e67e22;
            --p-gold: #f59e0b;
            --p-green: #059669;
            --p-blue: #2563eb;
        }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .navbar.bg-dark { background: linear-gradient(135deg,#1a1a2e 0%,#16213e 100%) !important; box-shadow: 0 2px 16px rgba(0,0,0,.15); }
        .navbar-brand { font-weight: 800; letter-spacing: -.5px; }
        .product-card { transition: all .3s cubic-bezier(.4,0,.2,1); border: none; box-shadow: 0 2px 8px rgba(0,0,0,.06); overflow: hidden; background: white; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
        .product-card .card-img-top { height: 220px; object-fit: cover; transition: transform .4s ease; }
        .product-card:hover .card-img-top { transform: scale(1.05); }
        .footer { background: linear-gradient(135deg,#1a1a2e 0%,#16213e 100%) !important; color: #94a3b8; padding: 3rem 0; margin-top: 4rem; }
        .price-tag { font-size: 1.35rem; font-weight: 800; color: var(--p-red); }
        .filter-section { background: white; border-radius: .75rem; padding: 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 1.5rem; }
        .btn-admin { background: linear-gradient(135deg,#1a1a2e,#16213e); color: white; border: none; }
        .btn-admin:hover { background: linear-gradient(135deg,#16213e,#1a1a2e); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,26,46,.3); }
        .btn-cart-main { background: linear-gradient(135deg,#e67e22,#d35400); color: white; border: none; padding: .8rem 1.5rem; font-size: 1.15rem; border-radius: .5rem; font-weight: 700; transition: all .3s; }
        .btn-cart-main:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(230,126,34,.35); color: white; }
        .btn-buy-main { background: linear-gradient(135deg,#e63946,#c1121f); color: white; border: none; padding: .8rem 2rem; font-size: 1.15rem; font-weight: 700; border-radius: .5rem; transition: all .3s; }
        .btn-buy-main:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(230,57,70,.35); color: white; }
        .section-title { font-weight: 800; color: #1a1a2e; position: relative; padding-bottom: .5rem; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 3px; background: linear-gradient(90deg,#e63946,#e67e22); border-radius: 2px; }
        .badge-available { background: linear-gradient(135deg,#059669,#047857); color: white; font-weight: 600; }
        .badge-limited { background: linear-gradient(135deg,#f59e0b,#d97706); color: white; font-weight: 600; }
        .badge-soldout { background: linear-gradient(135deg,#ef4444,#dc2626); color: white; font-weight: 600; }

        @media (min-width: 992px) {
            .container { max-width: 100%; padding-left: 2rem; padding-right: 2rem; }
        }
    </style>
    @stack('styles')

    @if(config('services.meta.pixel_id'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.meta.pixel_id') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ config('services.meta.pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
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
    @stack('pixel_events')
    @if(session('pixel_add_to_cart'))
    <script>fbq('track', 'AddToCart', @json(session('pixel_add_to_cart')));</script>
    @endif
    @stack('scripts')
</body>
</html>
