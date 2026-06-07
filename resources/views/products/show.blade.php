@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    .gallery-wrapper { position: relative; }
    .gallery-main-container { position: relative; overflow: hidden; border-radius: .75rem; cursor: crosshair; background: #fff; }
    .gallery-main-container .main-img { width: 100%; height: 500px; object-fit: cover; display: block; }
    .gallery-lens { position: absolute; display: none; width: 150px; height: 150px; border: 2px solid #212529; background: rgba(255,255,255,.3); pointer-events: none; z-index: 5; }
    .gallery-zoom { display: none; position: absolute; top: 0; left: calc(100% + 20px); width: 100%; height: 500px; border: 1px solid #dee2e6; border-radius: .75rem; overflow: hidden; background: #fff; z-index: 10; box-shadow: 0 8px 32px rgba(0,0,0,.15); }
    .gallery-zoom img { position: absolute; max-width: none; max-height: none; }
    .promo-badge { position: absolute; top: 1rem; left: 1rem; background: #dc3545; color: white; padding: .35rem .85rem; border-radius: 2rem; font-weight: 700; font-size: .9rem; z-index: 2; }
    .thumb-img { width: 80px; height: 80px; object-fit: cover; border-radius: .5rem; cursor: pointer; border: 2px solid transparent; transition: all .2s; opacity: .7; }
    .thumb-img:hover, .thumb-img.active { border-color: #212529; opacity: 1; }
    .original-price { text-decoration: line-through; color: #adb5bd; font-size: 1.2rem; }
    .promotion-price { font-size: 2.2rem; font-weight: 800; color: #dc3545; }
    .normal-price { font-size: 2.2rem; font-weight: 800; color: #212529; }
    .btn-buy { background: linear-gradient(135deg,#e63946,#c1121f); color: white; padding: .8rem 2rem; font-size: 1.15rem; font-weight: 700; border: none; border-radius: .5rem; transition: all .3s; }
    .btn-buy:hover { background: #bb2d3b; color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(230,57,70,.35); }
    .btn-cart { background: linear-gradient(135deg,#e67e22,#d35400); color: white; padding: .8rem 1.5rem; font-size: 1.15rem; border-radius: .5rem; border: none; font-weight: 700; transition: all .3s; }
    .btn-cart:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(230,126,34,.35); color: white; }
    .feature-icon { width: 48px; height: 48px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #212529; }

    /* Lightbox modal */
    .lightbox-img { max-height: 80vh; object-fit: contain; width: 100%; }
    .lightbox-btn { position: absolute; top: 50%; transform: translateY(-50%); z-index: 1060; background: rgba(0,0,0,.6); color: white; border: none; width: 48px; height: 48px; border-radius: 50%; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .2s; }
    .lightbox-btn:hover { background: rgba(0,0,0,.9); color: white; }
    .lightbox-prev { left: 10px; }
    .lightbox-next { right: 10px; }
    .lightbox-counter { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: white; background: rgba(0,0,0,.6); padding: .3rem 1rem; border-radius: 2rem; font-size: .9rem; z-index: 1060; }
    .modal-content.lightbox-modal { background: transparent; border: none; }
    .modal-content.lightbox-modal .btn-close { filter: invert(1); position: absolute; top: -40px; right: 0; z-index: 1060; }

    @media (max-width: 992px) {
        .gallery-zoom { display: none !important; }
        .gallery-lens { display: none !important; }
        .gallery-main-container { cursor: default; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Productos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    @php
        $galleryImages = collect();
        if ($product->image) {
            $galleryImages->push(['url' => $product->image, 'is_primary' => true]);
        }
        foreach ($product->images as $img) {
            if ($img->url !== $product->image) {
                $galleryImages->push(['url' => $img->url, 'is_primary' => false]);
            }
        }
        $galleryImages = $galleryImages->filter()->values();
        $totalGallery = $galleryImages->count();
    @endphp

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="gallery-wrapper position-relative">
                @if($product->hasActivePromotion())
                    <span class="promo-badge">{{ $product->promotionPercentage() }}% OFF</span>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="gallery-main-container" id="galleryMain">
                            <img id="mainImage"
                                 src="{{ $galleryImages->isNotEmpty() ? url('imgProduct/' . $galleryImages[0]['url']) : 'https://picsum.photos/seed/default/800/800' }}"
                                 class="main-img"
                                 alt="{{ $product->name }}"
                                 data-zoom="{{ $galleryImages->isNotEmpty() ? url('imgProduct/' . $galleryImages[0]['url']) : 'https://picsum.photos/seed/default/1200/1200' }}">
                            <div class="gallery-lens" id="galleryLens"></div>
                            <div class="gallery-zoom" id="galleryZoom">
                                <img id="zoomResult" src="{{ $galleryImages->isNotEmpty() ? url('imgProduct/' . $galleryImages[0]['url']) : 'https://picsum.photos/seed/default/1200/1200' }}">
                            </div>
                        </div>
                    </div>
                </div>

                @if($totalGallery > 1)
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($galleryImages as $idx => $img)
                            <img src="{{ url('imgProduct/' . $img['url']) }}"
                                 class="thumb-img {{ $idx === 0 ? 'active' : '' }}"
                                 onclick="switchImage(this, '{{ url('imgProduct/' . $img['url']) }}', {{ $idx }})">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <h1 class="fw-bold mb-2">{{ $product->name }}</h1>
            <p class="text-muted mb-3">
                <i class="bi bi-tag"></i> {{ $product->category->name }}
                <span class="mx-2">|</span>
                <i class="bi bi-box"></i> SKU: {{ $product->slug }}
            </p>

            <div class="mb-4">
                @if($product->hasActivePromotion())
                    <span class="original-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="promotion-price ms-2">${{ number_format($product->promotion_price, 0, ',', '.') }}</span>
                    <span class="badge bg-danger ms-2" style="font-size:.8rem;">-{{ $product->promotionPercentage() }}%</span>
                @else
                    <span class="normal-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
            </div>

            <div class="mb-4 d-flex align-items-center gap-2">
                <strong>Stock:</strong>
                @if($product->stock > 10)
                    <span class="badge badge-available fs-6">{{ $product->stock }} disponibles</span>
                @elseif($product->stock > 0)
                    <span class="badge badge-limited fs-6">Solo {{ $product->stock }} restantes</span>
                @else
                    <span class="badge badge-soldout fs-6">Agotado</span>
                @endif
            </div>

            <p class="lead mb-4">{{ $product->description }}</p>

            @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <div class="input-group" style="width:130px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="this.nextElementSibling.stepDown();">-</button>
                                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                                <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.stepUp();">+</button>
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-cart btn-outline-dark w-100">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-buy w-100">
                        <i class="bi bi-lightning-fill"></i> Comprar Ahora
                    </button>
                </form>
            @endif

            <hr>

            <div class="row g-3">
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-truck"></i></div>
                        <div><small class="text-muted d-block">Envío</small><strong>Rápido y Seguro</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <div><small class="text-muted d-block">Pago</small><strong>100% Seguro</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <div><small class="text-muted d-block">Cambios</small><strong>30 Días</strong></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="feature-icon"><i class="bi bi-headset"></i></div>
                        <div><small class="text-muted d-block">Soporte</small><strong>24/7</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <h3 class="mt-5 mb-4 fw-bold">Productos Relacionados</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
                <div class="col-sm-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($related->hasActivePromotion())
                                <span class="promo-badge" style="left:.5rem;top:.5rem;font-size:.75rem;padding:.2rem .6rem;">-{{ $related->promotionPercentage() }}%</span>
                            @endif
                            <img src="{{ $related->image ? url('imgProduct/' . $related->image) : 'https://picsum.photos/seed/default/600/600' }}" class="card-img-top" alt="{{ $related->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $related->name }}</h6>
                            <div class="mt-auto">
                                @if($related->hasActivePromotion())
                                    <small class="text-decoration-line-through text-muted">${{ number_format($related->price, 0, ',', '.') }}</small>
                                    <span class="price-tag">${{ number_format($related->promotion_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-tag">${{ number_format($related->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $related->slug) }}" class="btn btn-outline-dark btn-sm mt-2">Ver</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content lightbox-modal">
            <div class="position-relative text-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                <button class="lightbox-btn lightbox-prev" onclick="lightboxNav(-1)"><i class="bi bi-chevron-left"></i></button>
                <img id="lightboxImage" class="lightbox-img" src="" alt="{{ $product->name }}">
                <button class="lightbox-btn lightbox-next" onclick="lightboxNav(1)"><i class="bi bi-chevron-right"></i></button>
                <div class="lightbox-counter" id="lightboxCounter"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentIndex = 0;
const galleryUrls = [
    @foreach($galleryImages as $img)
        '{{ url('imgProduct/' . $img['url']) }}',
    @endforeach
];

// ── Thumbnail switcher ──
function switchImage(el, url, idx) {
    currentIndex = idx;
    document.getElementById('mainImage').src = url;
    document.getElementById('mainImage').dataset.zoom = url;
    document.getElementById('zoomResult').src = url;
    document.querySelectorAll('.thumb-img').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}

// ── Magnifier zoom ──
(function() {
    const container = document.getElementById('galleryMain');
    const lens = document.getElementById('galleryLens');
    const zoom = document.getElementById('galleryZoom');
    const zoomImg = document.getElementById('zoomResult');

    if (!container || window.innerWidth < 992) return;

    container.addEventListener('mouseenter', function() {
        if (galleryUrls.length === 0) return;
        lens.style.display = 'block';
        zoom.style.display = 'block';
    });

    container.addEventListener('mouseleave', function() {
        lens.style.display = 'none';
        zoom.style.display = 'none';
    });

    container.addEventListener('mousemove', function(e) {
        const rect = container.getBoundingClientRect();
        const img = container.querySelector('.main-img');
        const imgRect = img.getBoundingClientRect();

        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;

        const lensW = lens.offsetWidth;
        const lensH = lens.offsetHeight;

        let lensX = x - lensW / 2;
        let lensY = y - lensH / 2;

        if (lensX < 0) lensX = 0;
        if (lensY < 0) lensY = 0;
        if (lensX > rect.width - lensW) lensX = rect.width - lensW;
        if (lensY > rect.height - lensH) lensY = rect.height - lensH;

        lens.style.left = lensX + 'px';
        lens.style.top = lensY + 'px';

        const cx = rect.width / (rect.width - lensW);
        const cy = rect.height / (rect.height - lensH);

        const zoomLevel = 2.5;
        zoomImg.style.width = (rect.width * zoomLevel) + 'px';
        zoomImg.style.height = (rect.height * zoomLevel) + 'px';
        zoomImg.style.left = -(lensX * zoomLevel) + 'px';
        zoomImg.style.top = -(lensY * zoomLevel) + 'px';
    });
})();

// ── Lightbox ──
const lightboxModal = document.getElementById('lightboxModal');
const lightboxImg = document.getElementById('lightboxImage');
const lightboxCounter = document.getElementById('lightboxCounter');

function openLightbox(idx) {
    currentIndex = idx;
    updateLightbox();
    const modal = new bootstrap.Modal(lightboxModal);
    modal.show();
}

function updateLightbox() {
    if (galleryUrls.length === 0) return;
    lightboxImg.src = galleryUrls[currentIndex];
    lightboxCounter.textContent = (currentIndex + 1) + ' / ' + galleryUrls.length;
}

function lightboxNav(dir) {
    currentIndex += dir;
    if (currentIndex < 0) currentIndex = galleryUrls.length - 1;
    if (currentIndex >= galleryUrls.length) currentIndex = 0;
    updateLightbox();
}

document.addEventListener('keydown', function(e) {
    if (!lightboxModal.classList.contains('show')) return;
    if (e.key === 'ArrowLeft') lightboxNav(-1);
    if (e.key === 'ArrowRight') lightboxNav(1);
    if (e.key === 'Escape') bootstrap.Modal.getInstance(lightboxModal).hide();
});

// Click on main image to open lightbox
document.getElementById('mainImage').addEventListener('click', function() {
    openLightbox(currentIndex);
});
</script>
@endpush
