@extends('layouts.frontend')

@section('title', $item->value('hero_title') ?: $item->name())

@section('content')

@php
    $heroTitle    = $item->value('hero_title') ?: 'Welcome';
    $heroSub      = $item->value('hero_subtitle');
    $introTitle   = $item->value('intro_title');
    $introText    = $item->value('intro_text');

    // Fetch latest blog posts
    $blogPosts = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'blog_post'))
        ->orderByDesc('created_at')
        ->limit(3)
        ->get();

    // Fetch featured products
    $products = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'product'))
        ->limit(3)
        ->get();
@endphp

{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<div class="hero">
    <div class="hero-content">
        <h1 class="hero-title">{!! nl2br(e($heroTitle)) !!}</h1>
        @if($heroSub)
            <p class="hero-sub">{{ $heroSub }}</p>
        @endif
        <div class="hero-cta">
            <a href="/products" class="btn-primary">Explore Products</a>
            <a href="/about-us" class="btn-secondary">Learn More</a>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-graphic">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
            <div class="hero-badge">
                <div style="font-size:32px">&#9670;</div>
                <div style="font-size:12px;font-weight:700;letter-spacing:1px;margin-top:4px">MARBLE CMS</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Features ────────────────────────────────────────────────────── --}}
@if($introTitle || $introText)
<section class="home-section">
    <div class="features-header">
        @if($introTitle)<h2 class="section-title">{{ $introTitle }}</h2>@endif
        @if($introText)<div class="section-sub intro-text">{!! $introText !!}</div>@endif
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
</section>
@endif

{{-- ── Latest Blog Posts ────────────────────────────────────────────── --}}
@if($blogPosts->isNotEmpty())
<section class="home-section">
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
                $postUrl    = \Marble\Admin\Facades\Marble::url($post);
                $teaser     = $post->value('teaser');
                $author     = $post->value('author');
                $pubDate    = $post->value('publish_date');
            @endphp
            <article class="blog-card">
                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        @if($pubDate)<span>{{ \Carbon\Carbon::parse($pubDate)->format('M j, Y') }}</span>@endif
                        @if($author)<span class="blog-card-author">by {{ $author }}</span>@endif
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
</section>
@endif

{{-- ── Products ────────────────────────────────────────────────────── --}}
@if($products->isNotEmpty())
<section class="home-section">
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
                @if($badge)
                    <span class="product-badge">{{ $badge }}</span>
                @endif
                <h3 class="product-card-name">{{ $product->name() }}</h3>
                @if($tagline)
                    <p class="product-card-tagline">{{ $tagline }}</p>
                @endif
                @if($price)
                    <div class="product-card-price">{{ $price }}</div>
                @endif
                <a href="{{ $productUrl }}" class="btn-primary btn-sm">Learn more</a>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ── CTA Banner ──────────────────────────────────────────────────── --}}
<section class="cta-banner">
    <h2>Ready to get started?</h2>
    <p>Contact us today and see what Marble CMS can do for your project.</p>
    <a href="/contact" class="btn-primary btn-lg">Get in touch</a>
</section>

@endsection

@push('styles')
<style>
    /* ── Hero ── */
    .hero {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: center;
        background: linear-gradient(135deg, #0f1a2e 0%, #1a3a70 60%, #2258A8 100%);
        border-radius: 14px;
        padding: 60px 56px;
        margin-bottom: 48px;
        overflow: hidden;
        position: relative;
    }
    .hero-title {
        font-size: 42px;
        font-weight: 900;
        color: #fff;
        margin: 0 0 16px;
        line-height: 1.15;
        letter-spacing: -.5px;
    }
    .hero-sub {
        font-size: 17px;
        color: rgba(255,255,255,.75);
        margin: 0 0 32px;
        line-height: 1.6;
    }
    .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-primary {
        display: inline-block;
        background: #fff;
        color: #2258A8;
        font-weight: 700;
        font-size: 14px;
        padding: 11px 24px;
        border-radius: 6px;
        text-decoration: none;
        transition: box-shadow .15s, transform .1s;
        border: none;
        cursor: pointer;
    }
    .btn-primary:hover { box-shadow: 0 4px 16px rgba(0,0,0,.2); transform: translateY(-1px); text-decoration: none; color: #163C80; }
    .btn-secondary {
        display: inline-block;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.3);
        color: rgba(255,255,255,.9);
        font-weight: 600;
        font-size: 14px;
        padding: 11px 24px;
        border-radius: 6px;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-secondary:hover { background: rgba(255,255,255,.2); text-decoration: none; color: #fff; }
    .btn-sm { padding: 7px 16px; font-size: 13px; }
    .btn-lg { padding: 15px 36px; font-size: 16px; }

    .hero-visual { display: flex; align-items: center; justify-content: center; }
    .hero-graphic {
        width: 240px; height: 240px; position: relative;
        display: flex; align-items: center; justify-content: center;
    }
    .hero-orb {
        position: absolute; border-radius: 50%;
        animation: float 4s ease-in-out infinite;
    }
    .hero-orb-1 {
        width: 180px; height: 180px;
        background: radial-gradient(circle at 40% 40%, rgba(255,255,255,.15), rgba(255,255,255,.02));
        border: 1px solid rgba(255,255,255,.15);
    }
    .hero-orb-2 {
        width: 240px; height: 240px;
        background: radial-gradient(circle at 60% 60%, rgba(100,160,255,.08), transparent);
        border: 1px solid rgba(255,255,255,.06);
        animation-delay: 1s;
    }
    .hero-orb-3 {
        width: 120px; height: 120px;
        background: radial-gradient(circle, rgba(255,255,255,.1), transparent);
        animation-delay: .5s;
    }
    .hero-badge {
        position: relative; z-index: 2;
        text-align: center; color: #fff;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* ── Sections ── */
    .home-section { margin-bottom: 56px; }
    .section-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 16px;
    }
    .section-title { margin: 0 0 4px; }
    .section-sub { margin: 0; }
    .link-more {
        font-size: 13px;
        font-weight: 600;
        color: #2258A8;
        white-space: nowrap;
        padding-top: 4px;
    }
    .link-more:hover { text-decoration: underline; }

    /* ── Features ── */
    .features-header { text-align: center; margin-bottom: 32px; }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .feature-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        transition: box-shadow .15s, border-color .15s;
    }
    .feature-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); border-color: #c0d0ea; }
    .feature-icon { font-size: 28px; margin-bottom: 14px; }
    .feature-card h3 { font-size: 16px; font-weight: 700; margin: 0 0 8px; color: #0f1a2e; }
    .feature-card p { font-size: 13px; color: #666; margin: 0; line-height: 1.6; }

    /* ── Blog cards ── */
    .blog-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        overflow: hidden;
        transition: box-shadow .15s, border-color .15s;
    }
    .blog-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); border-color: #c0d0ea; }
    .blog-card-body { padding: 24px; }
    .blog-card-meta { font-size: 12px; color: #999; margin-bottom: 10px; display: flex; gap: 8px; flex-wrap: wrap; }
    .blog-card-author::before { content: '·'; margin-right: 8px; }
    .blog-card-title { font-size: 16px; font-weight: 700; margin: 0 0 10px; line-height: 1.4; }
    .blog-card-title a { color: #0f1a2e; }
    .blog-card-title a:hover { color: #2258A8; text-decoration: none; }
    .blog-card-teaser { font-size: 13px; color: #666; margin: 0 0 16px; line-height: 1.6; }
    .blog-card-link { font-size: 13px; font-weight: 600; color: #2258A8; }
    .blog-card-link:hover { text-decoration: underline; }

    /* ── Product cards ── */
    .product-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        position: relative;
        transition: box-shadow .15s, border-color .15s;
    }
    .product-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); border-color: #c0d0ea; }
    .product-badge {
        display: inline-block;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 12px;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .product-card-name { font-size: 17px; font-weight: 800; margin: 0 0 8px; color: #0f1a2e; }
    .product-card-tagline { font-size: 13px; color: #666; margin: 0 0 16px; line-height: 1.5; }
    .product-card-price { font-size: 20px; font-weight: 800; color: #2258A8; margin-bottom: 16px; }

    /* ── CTA Banner ── */
    .cta-banner {
        background: linear-gradient(135deg, #0f1a2e 0%, #2258A8 100%);
        border-radius: 14px;
        padding: 56px 48px;
        text-align: center;
        color: #fff;
        margin-bottom: 0;
    }
    .cta-banner h2 { font-size: 30px; font-weight: 800; margin: 0 0 12px; }
    .cta-banner p { font-size: 16px; color: rgba(255,255,255,.75); margin: 0 0 28px; }

    @media (max-width: 768px) {
        .hero { grid-template-columns: 1fr; padding: 36px 24px; }
        .hero-visual { display: none; }
        .hero-title { font-size: 28px; }
        .features-grid { grid-template-columns: 1fr 1fr; }
        .cta-banner { padding: 36px 24px; }
    }
    @media (max-width: 480px) {
        .features-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
