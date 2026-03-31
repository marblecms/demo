@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $description = $item->value('description');
    $icon        = $item->value('icon');

    $parent    = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
    $parentUrl = $parent ? \Marble\Admin\Facades\Marble::url($parent) : '/products';

    // Direct product children
    $products = \Marble\Admin\Models\Item::where('status', 'published')
        ->where('parent_id', $item->id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'product'))
        ->get();

    // Sub-category children
    $subcategories = \Marble\Admin\Models\Item::where('status', 'published')
        ->where('parent_id', $item->id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'product_category'))
        ->get();
@endphp

{{-- Breadcrumb --}}
<nav class="breadcrumb">
    <a href="/">Home</a>
    <span class="breadcrumb-sep">/</span>
    @if($parent && $parent->blueprint?->identifier === 'product_category')
        <a href="{{ $parentUrl }}">{{ $parent->name() }}</a>
        <span class="breadcrumb-sep">/</span>
    @else
        <a href="{{ $parentUrl }}">Products</a>
        <span class="breadcrumb-sep">/</span>
    @endif
    <span>{{ $item->name() }}</span>
</nav>

<div class="cat-header">
    @if($icon)<div class="cat-icon">{{ $icon }}</div>@endif
    <h1 class="cat-title">{{ $item->name() }}</h1>
    @if($description)<p class="cat-desc">{{ $description }}</p>@endif
</div>

{{-- Sub-categories --}}
@if($subcategories->isNotEmpty())
    <section class="cat-section">
        <h2 class="cat-section-title">Categories</h2>
        <div class="card-grid card-grid-sm">
            @foreach($subcategories as $sub)
                @php $subIcon = $sub->value('icon'); @endphp
                <a href="{{ \Marble\Admin\Facades\Marble::url($sub) }}" class="subcat-card">
                    @if($subIcon)<div class="subcat-icon">{{ $subIcon }}</div>@endif
                    <div class="subcat-name">{{ $sub->name() }}</div>
                    <div class="subcat-arrow">→</div>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- Products --}}
@if($products->isNotEmpty())
    <section class="cat-section">
        @if($subcategories->isNotEmpty())<h2 class="cat-section-title">Products</h2>@endif
        <div class="card-grid">
            @foreach($products as $product)
                @php
                    $productUrl = \Marble\Admin\Facades\Marble::url($product);
                    $tagline    = $product->value('tagline');
                    $price      = $product->value('price');
                    $badge      = $product->value('badge');
                    $featuresRaw = $product->value('features');
                    if (is_array($featuresRaw)) {
                        $featArr = array_filter(array_map(fn($f) => $f['feature'] ?? null, $featuresRaw));
                    } elseif (is_string($featuresRaw) && $featuresRaw !== '') {
                        $featArr = array_filter(array_map('trim', explode("\n", $featuresRaw)));
                    } else {
                        $featArr = [];
                    }
                    $topFeats = array_slice(array_values($featArr), 0, 3);
                @endphp
                <div class="product-list-card">
                    @if($badge)
                        <span class="product-badge">{{ $badge }}</span>
                    @endif
                    <h3 class="product-list-name">{{ $product->name() }}</h3>
                    @if($tagline)
                        <p class="product-list-tagline">{{ $tagline }}</p>
                    @endif
                    @if($topFeats)
                        <ul class="product-list-features">
                            @foreach($topFeats as $feat)
                                <li>{{ $feat }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="product-list-footer">
                        @if($price)<span class="product-list-price">{{ $price }}</span>@endif
                        <a href="{{ $productUrl }}" class="btn-outline">View details</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if($products->isEmpty() && $subcategories->isEmpty())
    <div class="content-card">
        <p style="color:#999;text-align:center;padding:40px 0">No products in this category yet.</p>
    </div>
@endif

@endsection

@push('styles')
<style>
    .cat-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .cat-icon { font-size: 48px; margin-bottom: 12px; }
    .cat-title {
        font-size: 32px;
        font-weight: 900;
        color: #0f1a2e;
        margin: 0 0 12px;
    }
    .cat-desc {
        font-size: 16px;
        color: #666;
        max-width: 540px;
        margin: 0 auto;
    }

    .cat-section { margin-bottom: 44px; }
    .cat-section-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e8edf3;
    }

    .subcat-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 8px;
        padding: 18px 20px;
        color: #2258A8;
        font-weight: 700;
        font-size: 15px;
        transition: background .15s, border-color .15s, box-shadow .15s;
        text-decoration: none;
    }
    .subcat-card:hover { background: #f0f4fb; border-color: #2258A8; box-shadow: 0 3px 12px rgba(34,88,168,.12); text-decoration: none; }
    .subcat-icon { font-size: 22px; }
    .subcat-name { flex: 1; }
    .subcat-arrow { color: #aac0e8; font-size: 18px; }

    .product-list-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        display: flex;
        flex-direction: column;
        gap: 0;
        transition: box-shadow .15s, border-color .15s;
    }
    .product-list-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); border-color: #c0d0ea; }

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
    .product-list-name { font-size: 18px; font-weight: 800; margin: 0 0 8px; color: #0f1a2e; }
    .product-list-tagline { font-size: 13px; color: #666; margin: 0 0 16px; line-height: 1.5; }
    .product-list-features {
        list-style: none;
        padding: 0;
        margin: 0 0 20px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .product-list-features li {
        font-size: 13px;
        color: #555;
        padding-left: 18px;
        position: relative;
    }
    .product-list-features li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #2258A8;
        font-weight: 700;
    }
    .product-list-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f0f4f8;
    }
    .product-list-price { font-size: 20px; font-weight: 800; color: #2258A8; }
    .btn-outline {
        display: inline-block;
        border: 2px solid #2258A8;
        color: #2258A8;
        font-weight: 700;
        font-size: 13px;
        padding: 7px 18px;
        border-radius: 6px;
        text-decoration: none;
        transition: background .15s, color .15s;
    }
    .btn-outline:hover { background: #2258A8; color: #fff; text-decoration: none; }
</style>
@endpush
