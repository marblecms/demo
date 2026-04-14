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

    $siblings   = \Marble\Admin\Models\Item::where('status', 'published')
        ->where('parent_id', $item->parent_id)
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'blog_post'))
        ->orderByDesc('created_at')
        ->get(['id', 'created_at']);

    $siblingIds = $siblings->pluck('id')->toArray();
    $pos        = array_search($item->id, $siblingIds);
    $prevPost   = $pos !== false && $pos + 1 < count($siblingIds) ? \Marble\Admin\Models\Item::find($siblingIds[$pos + 1]) : null;
    $nextPost   = $pos !== false && $pos > 0 ? \Marble\Admin\Models\Item::find($siblingIds[$pos - 1]) : null;
@endphp

<div class="blog-page-wrap">

    <x-breadcrumb :item="$item" />

    <article class="post-card">
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
            <p class="post-body" style="font-size:18px;color:#555;margin:0 0 32px;font-style:italic">{{ $teaser }}</p>
        @endif

        @if($content)
            <div class="post-body">{!! $content !!}</div>
        @endif
    </article>

    @if($prevPost || $nextPost)
        <nav class="post-nav" style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);border:1px solid var(--border);margin-bottom:40px">
            <div style="background:var(--white);padding:20px 24px">
                @if($prevPost)
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px">← Previous</div>
                    <a href="{{ \Marble\Admin\Facades\Marble::url($prevPost) }}" style="font-size:14px;font-weight:700">{{ $prevPost->name() }}</a>
                @endif
            </div>
            <div style="background:var(--white);padding:20px 24px;text-align:right">
                @if($nextPost)
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px">Next →</div>
                    <a href="{{ \Marble\Admin\Facades\Marble::url($nextPost) }}" style="font-size:14px;font-weight:700">{{ $nextPost->name() }}</a>
                @endif
            </div>
        </nav>
    @endif

    <div class="post-back">
        <a href="{{ $parentUrl }}" class="back-link">← Back to Blog</a>
    </div>

</div>

@endsection
