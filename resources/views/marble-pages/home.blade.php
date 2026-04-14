@extends('layouts.frontend')

@section('title', $item->value('hero_title') ?: $item->name())

@section('hero')@endsection
@section('content')

@php
    $heroTitle    = $item->value('hero_title') ?: 'Marble CMS';
    $heroSub      = $item->value('hero_subtitle');
    $introTitle   = $item->value('intro_title');
    $introText    = $item->value('intro_text');

    $blogPosts = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'blog_post'))
        ->orderByDesc('created_at')
        ->limit(3)
        ->get();

    $products = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'product'))
        ->limit(3)
        ->get();

    $screenshotsItem = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'screenshots_page'))
        ->first();
    $screenshots = $screenshotsItem?->value('screenshots') ?: [];
@endphp

{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<section class="hero" id="hero">
    <div class="hero-content">
        <h1 class="hero-title">{!! nl2br(e($heroTitle)) !!}</h1>
        @if($heroSub)
            <p class="hero-sub">{{ $heroSub }}</p>
        @endif
        <div class="hero-cta">
            <a href="/docs/introduction" class="btn-primary btn-lg">Get Started</a>
            <a href="/docs" class="btn-secondary btn-lg">Documentation</a>
        </div>
    </div>
</section>

{{-- ── Features ─────────────────────────────────────────────────── --}}
@if($introTitle || $introText)
<section class="home-section">
    <div class="wrap">
        <div class="features-header">
            @if($introTitle)<h2 class="section-title">{{ $introTitle }}</h2>@endif
            @if($introText)<div class="intro-text">{!! $introText !!}</div>@endif
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">&#128736;</div>
                <h3>Flexible Blueprints</h3>
                <p>Define your own content structures with fields, types, and validation — no code required.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#127760;</div>
                <h3>Multi-site Ready</h3>
                <p>Manage multiple websites and languages from a single installation with full locale support.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128274;</div>
                <h3>Role-based Access</h3>
                <p>Granular permissions per blueprint and user group — control who can create, read, update, or delete.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128203;</div>
                <h3>Workflow Engine</h3>
                <p>Built-in editorial workflows with approval steps, transitions, and assignment to user groups.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128272;</div>
                <h3>Portal Users</h3>
                <p>Gated content for registered members — run intranets, member areas, or customer portals.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#9881;</div>
                <h3>Headless API</h3>
                <p>Expose your content as JSON via the built-in REST API with token-based authentication.</p>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── Screenshots ──────────────────────────────────────────────── --}}
@if(!empty($screenshots))
<section class="home-section home-section-dark">
    <div class="wrap">
        <div class="section-header-row">
            <div>
                <h2 class="section-title">See it in action</h2>
                <p class="section-sub">A look inside the Marble CMS admin interface</p>
            </div>
            @if($screenshotsItem)
                <a href="{{ \Marble\Admin\Facades\Marble::url($screenshotsItem) }}" class="link-more">All screenshots →</a>
            @endif
        </div>

        <div class="carousel" id="marble-carousel">
            <div class="carousel-track-wrap">
                <div class="carousel-track">
                    @foreach($screenshots as $i => $shot)
                        <div class="carousel-slide {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}">
                            <img src="{{ $shot['url'] }}" alt="{{ $shot['original_filename'] ?? 'Screenshot ' . ($i + 1) }}" class="carousel-img">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-btn-fullscreen" aria-label="Fullscreen">&#x26F6;</button>
            </div>

            <button class="carousel-btn carousel-btn-prev" aria-label="Previous">&#8592;</button>
            <button class="carousel-btn carousel-btn-next" aria-label="Next">&#8594;</button>

            <div class="carousel-dots">
                @foreach($screenshots as $i => $shot)
                    <button class="carousel-dot {{ $i === 0 ? 'is-active' : '' }}" data-goto="{{ $i }}" aria-label="Go to screenshot {{ $i + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-counter">
                <span class="carousel-counter-current">1</span> / <span class="carousel-counter-total">{{ count($screenshots) }}</span>
            </div>
        </div>

        <div class="carousel-thumbnails">
            @foreach($screenshots as $i => $shot)
                <button class="carousel-thumb {{ $i === 0 ? 'is-active' : '' }}" data-goto="{{ $i }}" aria-label="Screenshot {{ $i + 1 }}">
                    <img src="{{ $shot['thumbnail'] ?? $shot['url'] }}" alt="">
                </button>
            @endforeach
        </div>
    </div>
</section>

<div class="carousel-lightbox" id="carousel-lightbox" aria-hidden="true">
    <button class="lightbox-close" aria-label="Close">&#x2715;</button>
    <button class="lightbox-prev" aria-label="Previous">&#8592;</button>
    <button class="lightbox-next" aria-label="Next">&#8594;</button>
    <div class="lightbox-img-wrap">
        <img class="lightbox-img" src="{{ isset($screenshots[0]) ? $screenshots[0]['url'] : "" }}" alt="">
    </div>
    <div class="lightbox-counter">
        <span class="lightbox-counter-current">1</span> / <span class="lightbox-counter-total">{{ count($screenshots) }}</span>
    </div>
</div>
@endif

{{-- ── Latest Blog Posts ─────────────────────────────────────────── --}}
@if($blogPosts->isNotEmpty())
<section class="home-section">
    <div class="wrap">
        <div class="section-header-row">
            <div>
                <h2 class="section-title">Latest from the Blog</h2>
                <p class="section-sub">Insights, tutorials and news from the Marble team</p>
            </div>
            <a href="/blog" class="link-more">View all posts →</a>
        </div>
        <div class="card-grid">
            @foreach($blogPosts as $post)
                @php
                    $postUrl = \Marble\Admin\Facades\Marble::url($post);
                    $teaser  = $post->value('teaser');
                    $author  = $post->value('author');
                    $pubDate = $post->value('publish_date');
                @endphp
                <article class="blog-card">
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            @if($pubDate)<span>{{ \Carbon\Carbon::parse($pubDate)->format('M j, Y') }}</span>@endif
                            @if($author)<span class="blog-card-author">{{ $author }}</span>@endif
                        </div>
                        <h3 class="blog-card-title">
                            <a href="{{ $postUrl }}">{{ $post->name() }}</a>
                        </h3>
                        @if($teaser)
                            <p class="blog-card-teaser">{{ Str::limit($teaser, 120) }}</p>
                        @endif
                        <a href="{{ $postUrl }}" class="blog-card-link">Read more →</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Products ──────────────────────────────────────────────────── --}}
@if($products->isNotEmpty())
<section class="home-section">
    <div class="wrap">
        <div class="section-header-row">
            <div>
                <h2 class="section-title">Our Products</h2>
                <p class="section-sub">Powerful software and services to grow your business</p>
            </div>
            <a href="/products" class="link-more">All products →</a>
        </div>
        <div class="card-grid">
            @foreach($products as $product)
                @php
                    $productUrl = \Marble\Admin\Facades\Marble::url($product);
                    $tagline    = $product->value('tagline');
                    $price      = $product->value('price');
                    $badge      = $product->value('badge');
                @endphp
                <div class="product-card">
                    @if($badge)<span class="product-badge">{{ $badge }}</span>@endif
                    <h3 class="product-card-name">{{ $product->name() }}</h3>
                    @if($tagline)<p class="product-card-tagline">{{ $tagline }}</p>@endif
                    @if($price)<div class="product-card-price">{{ $price }}</div>@endif
                    <a href="{{ $productUrl }}" class="btn-primary btn-sm">Learn more</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── CTA Banner ────────────────────────────────────────────────── --}}
<section class="cta-banner">
    <h2>Ready to get started?</h2>
    <p>Install Marble CMS in minutes and start building structured content for Laravel.</p>
    <a href="/docs" class="btn-primary btn-lg">Read the Docs</a>
</section>

@push('scripts')
<script>
(function () {
    var hero = document.getElementById('hero');
    if (!hero) return;

    /* ── Canvas overlay ─────────────────────────────────────────── */
    var canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;';
    hero.insertBefore(canvas, hero.firstChild);
    ['.hero-visual', '.hero-content'].forEach(function (sel) {
        var el = hero.querySelector(sel);
        if (el) el.style.position = 'relative';
    });
    var ctx = canvas.getContext('2d');

    /* ── Offscreen marble texture ───────────────────────────────── */
    var off    = document.createElement('canvas');
    var offCtx = off.getContext('2d');
    var W = 0, H = 0;

    function rnd(a, b) { return a + Math.random() * (b - a); }

    /* Crack propagation — each step deviates slightly, branches occasionally */
    function growCrack(c, x, y, angle, steps, lw, alpha, depth) {
        for (var i = 0; i < steps; i++) {
            angle += rnd(-0.22, 0.22);
            var nx = x + Math.cos(angle) * rnd(8, 18);
            var ny = y + Math.sin(angle) * rnd(8, 18);

            c.beginPath();
            c.moveTo(x, y);
            c.lineTo(nx, ny);
            c.strokeStyle = 'rgba(255,255,255,' + alpha + ')';
            c.lineWidth   = lw;
            c.lineCap     = 'round';
            if (lw > 1.2) { c.shadowColor = 'rgba(255,255,255,0.25)'; c.shadowBlur = 4; }
            c.stroke();
            c.shadowBlur = 0;

            x = nx; y = ny;
            lw    *= 0.997;
            alpha *= 0.999;
            if (lw < 0.15) break;

            /* Occasional branch */
            if (depth > 0 && Math.random() < 0.055) {
                growCrack(c, x, y, angle + rnd(-0.7, 0.7),
                    Math.floor(steps * rnd(0.25, 0.55)),
                    lw * rnd(0.35, 0.65),
                    alpha * rnd(0.55, 0.85),
                    depth - 1);
            }
        }
    }

    function generateMarble() {
        var OW = off.width  = Math.round(W * 1.7);
        var OH = off.height = Math.round(H * 1.7);
        offCtx.clearRect(0, 0, OW, OH);

        var diag = Math.sqrt(OW * OW + OH * OH);

        /* 8 main veins from edges */
        for (var i = 0; i < 8; i++) {
            var edge = Math.floor(Math.random() * 4);
            var sx, sy, angle;
            if (edge === 0) { sx = rnd(0, OW);   sy = rnd(-10, 10);   angle = rnd(0.3, 1.1); }
            else if (edge === 1) { sx = rnd(-10, 10);  sy = rnd(0, OH);   angle = rnd(-0.4, 0.4); }
            else if (edge === 2) { sx = rnd(0, OW);   sy = OH + rnd(-10, 10); angle = rnd(-1.1, -0.3); }
            else               { sx = OW + rnd(-10, 10); sy = rnd(0, OH);  angle = Math.PI + rnd(-0.4, 0.4); }

            growCrack(offCtx, sx, sy, angle,
                Math.floor(diag / rnd(9, 14)),
                rnd(0.6, 2.2),
                rnd(0.22, 0.42),
                4);
        }

        /* 18 finer detail cracks scattered across the slab */
        for (var j = 0; j < 18; j++) {
            growCrack(offCtx,
                rnd(-0.05, 1.05) * OW,
                rnd(-0.05, 1.05) * OH,
                rnd(0, Math.PI * 2),
                Math.floor(rnd(6, 22)),
                rnd(0.25, 0.8),
                rnd(0.10, 0.22),
                2);
        }
    }

    function resize() {
        W = canvas.width  = hero.offsetWidth;
        H = canvas.height = hero.offsetHeight;
        generateMarble();
    }

    /* ── Cursor tracking ────────────────────────────────────────── */
    var targetX = 0, targetY = 0, curX = 0, curY = 0;
    hero.addEventListener('mousemove', function (e) {
        var r = hero.getBoundingClientRect();
        targetX = e.clientX - r.left - W / 2;
        targetY = e.clientY - r.top  - H / 2;
    });
    hero.addEventListener('mouseleave', function () { targetX = 0; targetY = 0; });

    /* ── Main loop — pans the offscreen texture slowly ─────────── */
    function tick(t) {
        curX += (targetX - curX) * 0.055;
        curY += (targetY - curY) * 0.055;

        /* Slow Lissajous drift — period ~2 min, never repeats exactly */
        var drift = Math.min(W, H) * 0.07;
        var driftX = Math.cos(t * 0.00008)           * drift;
        var driftY = Math.sin(t * 0.00008 * 0.618034) * drift;

        var ox = -((off.width  - W) / 2) + driftX + curX * 0.055;
        var oy = -((off.height - H) / 2) + driftY + curY * 0.055;

        ctx.clearRect(0, 0, W, H);
        ctx.drawImage(off, ox, oy);

        requestAnimationFrame(tick);
    }

    resize();
    window.addEventListener('resize', resize);
    requestAnimationFrame(tick);
})();
</script>
@endpush

@if(!empty($screenshots))
@push('scripts')
<script>
(function () {
    var carousel = document.getElementById('marble-carousel');
    if (!carousel) return;

    var slides     = carousel.querySelectorAll('.carousel-slide');
    var dots       = carousel.querySelectorAll('.carousel-dot');
    var thumbs     = document.querySelectorAll('.carousel-thumb');
    var counter    = carousel.querySelector('.carousel-counter-current');
    var total      = slides.length;
    var current    = 0;

    // Lightbox
    var lightbox  = document.getElementById('carousel-lightbox');
    var lbImg     = lightbox && lightbox.querySelector('.lightbox-img');
    var lbCurrent = lightbox && lightbox.querySelector('.lightbox-counter-current');

    function syncLightbox() {
        if (!lightbox || !lightbox.classList.contains('is-open')) return;
        var src = slides[current].querySelector('.carousel-img');
        if (lbImg)     { lbImg.src = src.src; lbImg.alt = src.alt; }
        if (lbCurrent) lbCurrent.textContent = current + 1;
    }

    function openLightbox() {
        if (!lightbox) return;
        syncLightbox();
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function goTo(n) {
        slides[current].classList.remove('is-active');
        dots[current].classList.remove('is-active');
        thumbs[current].classList.remove('is-active');

        current = (n + total) % total;

        slides[current].classList.add('is-active');
        dots[current].classList.add('is-active');
        thumbs[current].classList.add('is-active');
        if (counter) counter.textContent = current + 1;
        syncLightbox();
    }

    carousel.querySelector('.carousel-btn-prev').addEventListener('click', function () { goTo(current - 1); });
    carousel.querySelector('.carousel-btn-next').addEventListener('click', function () { goTo(current + 1); });

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () { goTo(parseInt(dot.dataset.goto)); });
    });
    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () { goTo(parseInt(thumb.dataset.goto)); });
    });

    var btnFs = carousel.querySelector('.carousel-btn-fullscreen');
    if (btnFs) btnFs.addEventListener('click', openLightbox);

    if (lightbox) {
        lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lightbox.querySelector('.lightbox-prev').addEventListener('click', function () { goTo(current - 1); });
        lightbox.querySelector('.lightbox-next').addEventListener('click', function () { goTo(current + 1); });
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox || e.target === lbImg.parentElement) closeLightbox();
        });
    }

    // Keyboard
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
        if (e.key === 'Escape')     closeLightbox();
    });
})();
</script>
@endpush
@endif

@endsection
