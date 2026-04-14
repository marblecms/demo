@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    use Marble\Admin\Models\Item;
    use Marble\Admin\Facades\Marble;

    $description = $item->value('description');

    $docsChildren = Item::where('status', 'published')
        ->where('parent_id', $item->id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'docs_page'))
        ->orderBy('sort_order')
        ->get();

    $changelogChildren = Item::where('status', 'published')
        ->where('parent_id', $item->id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'changelog_entry'))
        ->orderBy('sort_order')
        ->get();

    $grouped = $docsChildren->groupBy(fn($p) => $p->value('section') ?: 'General');
@endphp

{{-- Page header --}}
<div class="docs-index-header">
    <div class="wrap">
        <x-breadcrumb :item="$item" />
        <h1 class="docs-index-title">{{ $item->name() }}</h1>
        @if($description)
            <p class="docs-index-lead">{{ $description }}</p>
        @endif
    </div>
</div>

{{-- Docs overview --}}
@if($docsChildren->isNotEmpty())
<div class="docs-index-body">
    <div class="wrap">
        @foreach($grouped as $sectionName => $pages)
            <div class="docs-index-section">
                <div class="docs-index-section-label">{{ $sectionName }}</div>
                <div class="docs-index-grid">
                    @foreach($pages as $page)
                        @php $pageDesc = $page->value('description'); @endphp
                        <a href="{{ Marble::url($page) }}" class="docs-index-card">
                            <div class="docs-index-card-inner">
                                <div class="docs-index-card-title">{{ $page->name() }}</div>
                                @if($pageDesc)
                                    <div class="docs-index-card-desc">{{ Str::limit($pageDesc, 100) }}</div>
                                @endif
                            </div>
                            <div class="docs-index-card-arrow">→</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Changelog --}}
@if($changelogChildren->isNotEmpty())
<div class="docs-index-body">
    <div class="wrap">
        <div class="changelog-list">
            @foreach($changelogChildren as $entry)
                @php
                    $version     = $entry->value('version');
                    $releaseDate = $entry->value('release_date');
                @endphp
                <a href="{{ Marble::url($entry) }}" class="changelog-row">
                    <div class="changelog-row-left">
                        @if($version)
                            <span class="changelog-version-badge">v{{ $version }}</span>
                        @endif
                        <span class="changelog-row-title">{{ $entry->name() }}</span>
                    </div>
                    <div class="changelog-row-right">
                        @if($releaseDate)
                            <span class="changelog-date">{{ \Carbon\Carbon::parse($releaseDate)->format('F j, Y') }}</span>
                        @endif
                        <span class="changelog-row-arrow">→</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
