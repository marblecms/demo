@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    use Marble\Admin\Models\Item;
    use Marble\Admin\Facades\Marble;

    $description = $item->value('description');
    $content     = $item->value('content');

    // Load parent docs_section and all sibling docs_pages
    $parent = $item->parent_id ? Item::find($item->parent_id) : null;
    $parentUrl = $parent ? Marble::url($parent) : '/docs';

    $siblings = $parent
        ? Item::where('status', 'published')
            ->where('parent_id', $parent->id)
            ->whereHas('blueprint', fn($q) => $q->where('identifier', 'docs_page'))
            ->orderBy('sort_order')
            ->get()
        : collect();

    // Group siblings by their 'section' field value
    $grouped = $siblings->groupBy(fn($p) => $p->value('section') ?: 'General');

    // Calculate prev/next within siblings list
    $siblingIds = $siblings->pluck('id')->toArray();
    $pos        = array_search($item->id, $siblingIds);
    $prevPage   = ($pos !== false && $pos > 0) ? Item::find($siblingIds[$pos - 1]) : null;
    $nextPage   = ($pos !== false && $pos + 1 < count($siblingIds)) ? Item::find($siblingIds[$pos + 1]) : null;
@endphp

<div class="docs-layout">

    {{-- ── Sidebar ───────────────────────────────────────────────────────── --}}
    <aside class="docs-sidebar">
        <div class="docs-sidebar-inner">

            <div class="docs-sidebar-logo">
                <a href="{{ $parentUrl }}" class="docs-back-link">
                    ← {{ $parent ? $parent->name() : 'Documentation' }}
                </a>
            </div>

            <button class="docs-sidebar-toggle" aria-expanded="true" aria-controls="docs-nav">
                Navigation
            </button>

            <nav id="docs-nav" class="docs-nav">
                @foreach($grouped as $sectionName => $pages)
                    <div class="docs-nav-section">
                        <div class="docs-nav-section-title">{{ $sectionName }}</div>
                        @foreach($pages as $page)
                            <a
                                href="{{ Marble::url($page) }}"
                                class="docs-nav-item{{ $page->id === $item->id ? ' docs-nav-item--active' : '' }}"
                            >{{ $page->name() }}</a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

        </div>
    </aside>

    {{-- ── Main content ──────────────────────────────────────────────────── --}}
    <main class="docs-content">

        <x-breadcrumb :item="$item" />

        <h1 class="docs-title">{{ $item->name() }}</h1>

        @if($description)
            <p class="docs-lead">{{ $description }}</p>
        @endif

        @if($content)
            <div class="docs-body">{!! $content !!}</div>
        @endif

        {{-- ── Prev / Next pagination ───────────────────────────────────── --}}
        @if($prevPage || $nextPage)
            <nav class="docs-pagination">
                <div class="docs-pagination-cell">
                    @if($prevPage)
                        <a href="{{ Marble::url($prevPage) }}" class="docs-pagination-link">
                            <span class="docs-pagination-label">← Previous</span>
                            <span class="docs-pagination-title">{{ $prevPage->name() }}</span>
                        </a>
                    @endif
                </div>
                <div class="docs-pagination-cell docs-pagination-cell--right">
                    @if($nextPage)
                        <a href="{{ Marble::url($nextPage) }}" class="docs-pagination-link">
                            <span class="docs-pagination-label">Next →</span>
                            <span class="docs-pagination-title">{{ $nextPage->name() }}</span>
                        </a>
                    @endif
                </div>
            </nav>
        @endif

    </main>

</div>

@endsection
