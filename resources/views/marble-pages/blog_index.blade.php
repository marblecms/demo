@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $intro = $item->value('intro');

    $posts = \Marble\Admin\Models\Item::where('status', 'published')
        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'blog_post'))
        ->where('path', 'like', $item->path . '%')
        ->where('id', '!=', $item->id)
        ->orderByDesc('created_at')
        ->get()
        ->sortByDesc(fn($p) => $p->value('publish_date') ?: '')
        ->values();
@endphp

<div class="blog-header">
    <h1>{{ $item->name() }}</h1>
    @if($intro)<p class="blog-intro">{{ $intro }}</p>@endif
</div>

@if($posts->isEmpty())
    <div class="content-card">
        <p style="color:#999;text-align:center;padding:40px 0">No posts published yet.</p>
    </div>
@else
    <div class="blog-list">
        @foreach($posts as $post)
            @php
                $postUrl  = \Marble\Admin\Facades\Marble::url($post);
                $teaser   = $post->value('teaser');
                $author   = $post->value('author');
                $pubDate  = $post->value('publish_date');
            @endphp
            <article class="blog-list-item">
                <div class="blog-list-meta">
                    @if($pubDate)
                        <time>{{ \Carbon\Carbon::parse($pubDate)->format('F j, Y') }}</time>
                    @endif
                    @if($author)
                        <span class="blog-list-author">by {{ $author }}</span>
                    @endif
                </div>
                <h2 class="blog-list-title">
                    <a href="{{ $postUrl }}">{{ $post->name() }}</a>
                </h2>
                @if($teaser)
                    <p class="blog-list-teaser">{{ Str::limit($teaser, 200) }}</p>
                @endif
                <a href="{{ $postUrl }}" class="read-more-link">Read full article →</a>
            </article>
        @endforeach
    </div>
@endif

@endsection

@push('styles')
<style>
    .blog-header {
        text-align: center;
        margin-bottom: 36px;
    }
    .blog-header h1 {
        font-size: 34px;
        font-weight: 900;
        color: #0f1a2e;
        margin: 0 0 12px;
    }
    .blog-intro {
        font-size: 17px;
        color: #666;
        max-width: 540px;
        margin: 0 auto;
    }

    .blog-list { display: flex; flex-direction: column; gap: 16px; }

    .blog-list-item {
        background: #fff;
        border-radius: 10px;
        padding: 32px 36px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        transition: box-shadow .15s, border-color .15s;
    }
    .blog-list-item:hover { box-shadow: 0 4px 20px rgba(0,0,0,.09); border-color: #c0d0ea; }

    .blog-list-meta {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .blog-list-author { font-weight: 600; }
    .blog-list-author::before { content: '·'; margin-right: 6px; }

    .blog-list-title {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 12px;
        line-height: 1.35;
    }
    .blog-list-title a { color: #0f1a2e; }
    .blog-list-title a:hover { color: #2258A8; text-decoration: none; }

    .blog-list-teaser {
        font-size: 14px;
        color: #555;
        margin: 0 0 16px;
        line-height: 1.7;
    }
    .read-more-link {
        font-size: 13px;
        font-weight: 700;
        color: #2258A8;
    }
    .read-more-link:hover { text-decoration: underline; }
</style>
@endpush
