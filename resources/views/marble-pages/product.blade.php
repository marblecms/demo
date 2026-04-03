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
