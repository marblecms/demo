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
