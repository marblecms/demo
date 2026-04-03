@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $teaser    = $item->value('teaser');
    $content   = $item->value('content');
    $author    = $item->value('author');
    $pubDate   = $item->value('publish_date');

    $parent    = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
    $parentUrl = $parent ? \Marble\Admin\Facades\Marble::url($parent) : '/blog';

    // Previous / next post siblings
    $siblings = \Marble\Admin\Models\Item::where('status', 'published')
        ->where('parent_id', $item->parent_id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'blog_post'))
        ->orderByDesc('created_at')
        ->get(['id', 'created_at']);

    $siblingIds = $siblings->pluck('id')->toArray();
    $pos        = array_search($item->id, $siblingIds);
    $prevPost   = $pos !== false && $pos + 1 < count($siblingIds)
        ? \Marble\Admin\Models\Item::find($siblingIds[$pos + 1]) : null;
    $nextPost   = $pos !== false && $pos > 0
        ? \Marble\Admin\Models\Item::find($siblingIds[$pos - 1]) : null;
@endphp

{{-- Breadcrumb --}}
<nav class="breadcrumb" aria-label="breadcrumb">
    <a href="/">Home</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ $parentUrl }}">{{ $parent?->name() ?? 'Blog' }}</a>
    <span class="breadcrumb-sep">/</span>
    <span>{{ Str::limit($item->name(), 50) }}</span>
</nav>

<article class="post-card">
    {{-- Post meta --}}
    <div class="post-meta">
        @if($pubDate)
            <time class="post-date">{{ \Carbon\Carbon::parse($pubDate)->format('F j, Y') }}</time>
        @endif
        @if($author)
            <span class="post-author-tag">
                <span class="post-author-avatar">{{ strtoupper(substr($author, 0, 1)) }}</span>
                {{ $author }}
            </span>
        @endif
    </div>

    <h1 class="post-title">{{ $item->name() }}</h1>

    @if($teaser)
        <p class="post-teaser">{{ $teaser }}</p>
    @endif

    @if($content)
        <div class="post-content">
            {!! $content !!}
        </div>
    @endif
</article>

{{-- Post navigation --}}
@if($prevPost || $nextPost)
    <nav class="post-nav">
        <div class="post-nav-prev">
            @if($prevPost)
                <span class="post-nav-label">← Previous</span>
                <a href="{{ \Marble\Admin\Facades\Marble::url($prevPost) }}" class="post-nav-link">{{ $prevPost->name() }}</a>
            @endif
        </div>
        <div class="post-nav-next">
            @if($nextPost)
                <span class="post-nav-label">Next →</span>
                <a href="{{ \Marble\Admin\Facades\Marble::url($nextPost) }}" class="post-nav-link">{{ $nextPost->name() }}</a>
            @endif
        </div>
    </nav>
@endif

<div style="text-align:center;margin-top:12px">
    <a href="{{ $parentUrl }}" class="back-link">← Back to Blog</a>
</div>

@endsection
