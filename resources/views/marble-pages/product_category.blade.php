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
<x-breadcrumb :item="$item" />

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
