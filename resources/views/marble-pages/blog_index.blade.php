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

<div class="blog-index-header">
    <div class="wrap">
        <x-breadcrumb :item="$item" />
        <h1>{{ $item->name() }}</h1>
        @if($intro)<p class="section-sub">{{ $intro }}</p>@endif
    </div>
</div>

<div class="wrap" style="padding-top:48px;padding-bottom:80px">
    @if($posts->isEmpty())
        <p style="color:#999;padding:40px 0;text-align:center">No posts published yet.</p>
    @else
        <div class="blog-index-list">
            @foreach($posts as $post)
                @php
                    $postUrl = \Marble\Admin\Facades\Marble::url($post);
                    $teaser  = $post->value('teaser');
                    $author  = $post->value('author');
                    $pubDate = $post->value('publish_date');
                @endphp
                <article class="blog-index-post">
                    <div>
                        <div class="blog-index-meta">
                            @if($pubDate){{ \Carbon\Carbon::parse($pubDate)->format('F j, Y') }}@endif
                            @if($author) · {{ $author }}@endif
                        </div>
                        <h2 class="blog-index-title">
                            <a href="{{ $postUrl }}">{{ $post->name() }}</a>
                        </h2>
                        @if($teaser)<p class="blog-index-teaser">{{ Str::limit($teaser, 200) }}</p>@endif
                    </div>
                    <a href="{{ $postUrl }}" class="blog-index-link">Read →</a>
                </article>
            @endforeach
        </div>
    @endif
</div>

@endsection
