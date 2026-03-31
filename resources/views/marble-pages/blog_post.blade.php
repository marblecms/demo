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

@push('styles')
<style>
    .post-card {
        background: #fff;
        border-radius: 10px;
        padding: 48px 56px;
        box-shadow: 0 1px 8px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
        margin-bottom: 20px;
    }

    .post-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .post-date {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }
    .post-author-tag {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
    }
    .post-author-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-size: 12px; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
    }

    .post-title {
        font-size: 32px;
        font-weight: 900;
        color: #0f1a2e;
        margin: 0 0 20px;
        line-height: 1.25;
        letter-spacing: -.3px;
    }

    .post-teaser {
        font-size: 17px;
        color: #555;
        line-height: 1.7;
        margin: 0 0 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid #eef0f4;
        font-style: italic;
    }

    .post-content {
        font-size: 15px;
        line-height: 1.85;
        color: #333;
    }
    .post-content h2 {
        font-size: 22px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 36px 0 14px;
        padding-top: 8px;
        border-top: 2px solid #eef0f4;
    }
    .post-content h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1a2e50;
        margin: 28px 0 10px;
    }
    .post-content p { margin: 0 0 18px; }
    .post-content ul, .post-content ol { margin: 0 0 18px 20px; }
    .post-content li { margin-bottom: 6px; }
    .post-content blockquote {
        border-left: 4px solid #2258A8;
        margin: 24px 0;
        padding: 12px 20px;
        background: #f5f8ff;
        color: #445;
        font-style: italic;
        border-radius: 0 6px 6px 0;
    }
    .post-content img { max-width: 100%; border-radius: 8px; margin: 8px 0; }
    .post-content a { color: #2258A8; font-weight: 500; }
    .post-content code {
        background: #f0f4f8;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 13px;
        font-family: 'SF Mono', Monaco, 'Consolas', monospace;
    }

    .post-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .post-nav-prev, .post-nav-next {
        background: #fff;
        border-radius: 8px;
        padding: 18px 22px;
        border: 1px solid #e8edf3;
    }
    .post-nav-next { text-align: right; }
    .post-nav-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .8px; color: #aaa; margin-bottom: 4px; }
    .post-nav-link { font-size: 14px; font-weight: 700; color: #2258A8; }
    .post-nav-link:hover { text-decoration: underline; }

    .back-link { font-size: 13px; font-weight: 600; color: #2258A8; }
    .back-link:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .post-card { padding: 28px 20px; }
        .post-title { font-size: 24px; }
        .post-nav { grid-template-columns: 1fr; }
    }
</style>
@endpush
