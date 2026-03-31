@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $tagline     = $item->value('tagline');
    $description = $item->value('description');
    $price       = $item->value('price');
    $badge       = $item->value('badge');
    $featuresRaw = $item->value('features');
    // Support both repeater array [{feature:...}] and plain newline-separated string
    if (is_array($featuresRaw)) {
        $features = array_filter(array_map(fn($f) => $f['feature'] ?? null, $featuresRaw));
    } elseif (is_string($featuresRaw) && $featuresRaw !== '') {
        $features = array_filter(array_map('trim', explode("\n", $featuresRaw)));
    } else {
        $features = [];
    }

    $parent    = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
    $parentUrl = $parent ? \Marble\Admin\Facades\Marble::url($parent) : '/products';

    // Related products (siblings, up to 3)
    $related = \Marble\Admin\Models\Item::where('status', 'published')
        ->where('parent_id', $item->parent_id)
        ->where('id', '!=', $item->id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'product'))
        ->limit(3)
        ->get();
@endphp

{{-- Breadcrumb --}}
<nav class="breadcrumb">
    <a href="/">Home</a>
    <span class="breadcrumb-sep">/</span>
    <a href="/products">Products</a>
    @if($parent && $parent->blueprint?->identifier === 'product_category')
        <span class="breadcrumb-sep">/</span>
        <a href="{{ $parentUrl }}">{{ $parent->name() }}</a>
    @endif
    <span class="breadcrumb-sep">/</span>
    <span>{{ $item->name() }}</span>
</nav>

<div class="product-detail">
    <div class="product-detail-main">
        <div class="product-detail-card">
            @if($badge)
                <span class="product-badge-lg">{{ $badge }}</span>
            @endif
            <h1 class="product-detail-title">{{ $item->name() }}</h1>
            @if($tagline)
                <p class="product-detail-tagline">{{ $tagline }}</p>
            @endif
            @if($description)
                <div class="product-detail-desc">{!! $description !!}</div>
            @endif

            @if(!empty($features))
                <div class="product-features-block">
                    <h3 class="product-features-title">What's included</h3>
                    <ul class="product-features-list">
                        @foreach($features as $feat)
                            <li>
                                <span class="feat-check">✓</span>
                                {{ $feat }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="product-detail-sidebar">
        <div class="product-pricing-card">
            @if($price)
                <div class="pricing-amount">{{ $price }}</div>
                <div class="pricing-label">Starting price</div>
            @else
                <div class="pricing-amount">Contact us</div>
                <div class="pricing-label">For pricing</div>
            @endif
            <a href="/contact" class="btn-buy">Get started</a>
            <a href="/contact" class="btn-contact">Request a demo</a>
            <div class="pricing-trust">
                <div class="trust-item">✓ No setup fees</div>
                <div class="trust-item">✓ Cancel anytime</div>
                <div class="trust-item">✓ 30-day free trial</div>
            </div>
        </div>
    </div>
</div>

{{-- Related products --}}
@if($related->isNotEmpty())
    <section class="related-section">
        <h2 class="related-title">Related Products</h2>
        <div class="card-grid">
            @foreach($related as $rel)
                @php
                    $relUrl     = \Marble\Admin\Facades\Marble::url($rel);
                    $relTagline = $rel->value('tagline');
                    $relBadge   = $rel->value('badge');
                    $relPrice   = $rel->value('price');
                @endphp
                <div class="product-list-card">
                    @if($relBadge)<span class="product-badge">{{ $relBadge }}</span>@endif
                    <h3 class="product-list-name">{{ $rel->name() }}</h3>
                    @if($relTagline)<p class="product-list-tagline">{{ $relTagline }}</p>@endif
                    <div class="product-list-footer">
                        @if($relPrice)<span class="product-list-price">{{ $relPrice }}</span>@endif
                        <a href="{{ $relUrl }}" class="btn-outline">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@endsection

@push('styles')
<style>
    .product-detail {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 28px;
        align-items: start;
    }

    .product-detail-card {
        background: #fff;
        border-radius: 10px;
        padding: 40px 44px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
    }

    .product-badge-lg {
        display: inline-block;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 16px;
        letter-spacing: .5px;
        text-transform: uppercase;
    }
    .product-detail-title {
        font-size: 30px;
        font-weight: 900;
        color: #0f1a2e;
        margin: 0 0 12px;
        line-height: 1.2;
    }
    .product-detail-tagline {
        font-size: 17px;
        color: #555;
        margin: 0 0 24px;
        font-style: italic;
        line-height: 1.6;
    }
    .product-detail-desc {
        font-size: 15px;
        line-height: 1.8;
        color: #333;
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #eef0f4;
    }
    .product-detail-desc h2, .product-detail-desc h3 { color: #0f1a2e; font-weight: 800; }
    .product-detail-desc p { margin: 0 0 14px; }

    .product-features-block { }
    .product-features-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 16px;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-size: 12px;
        color: #999;
    }
    .product-features-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .product-features-list li {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        color: #444;
    }
    .feat-check {
        color: #2258A8;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Sidebar pricing card */
    .product-pricing-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 1px 10px rgba(0,0,0,.09);
        border: 1px solid #e8edf3;
        text-align: center;
        position: sticky;
        top: 80px;
    }
    .pricing-amount {
        font-size: 36px;
        font-weight: 900;
        color: #2258A8;
        line-height: 1;
        margin-bottom: 4px;
    }
    .pricing-label {
        font-size: 12px;
        color: #999;
        margin-bottom: 24px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .btn-buy {
        display: block;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        padding: 13px 24px;
        border-radius: 7px;
        text-decoration: none;
        margin-bottom: 10px;
        transition: opacity .15s, transform .1s;
    }
    .btn-buy:hover { opacity: .9; transform: translateY(-1px); text-decoration: none; color: #fff; }
    .btn-contact {
        display: block;
        border: 2px solid #2258A8;
        color: #2258A8;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 24px;
        border-radius: 7px;
        text-decoration: none;
        margin-bottom: 20px;
        transition: background .15s;
    }
    .btn-contact:hover { background: #f0f4fb; text-decoration: none; }
    .pricing-trust {
        border-top: 1px solid #eef0f4;
        padding-top: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .trust-item { font-size: 12px; color: #777; }
    .trust-item::before { color: #27ae60; margin-right: 4px; }

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
        align-self: flex-start;
    }
    .product-list-card {
        background: #fff;
        border-radius: 10px;
        padding: 24px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        display: flex;
        flex-direction: column;
        gap: 0;
        transition: box-shadow .15s;
    }
    .product-list-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); border-color: #c0d0ea; }
    .product-list-name { font-size: 17px; font-weight: 800; margin: 0 0 6px; color: #0f1a2e; }
    .product-list-tagline { font-size: 13px; color: #666; margin: 0 0 14px; }
    .product-list-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 14px; border-top: 1px solid #f0f4f8; }
    .product-list-price { font-size: 18px; font-weight: 800; color: #2258A8; }
    .btn-outline {
        display: inline-block;
        border: 2px solid #2258A8;
        color: #2258A8;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: 6px;
        text-decoration: none;
        transition: background .15s, color .15s;
    }
    .btn-outline:hover { background: #2258A8; color: #fff; text-decoration: none; }

    .related-section { margin-top: 40px; }
    .related-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8edf3;
    }

    @media (max-width: 768px) {
        .product-detail { grid-template-columns: 1fr; }
        .product-detail-card { padding: 24px 20px; }
        .product-features-list { grid-template-columns: 1fr; }
        .product-pricing-card { position: static; }
    }
</style>
@endpush
