@extends('layouts.frontend')

@section('title', 'Search' . ($query ? ': ' . $query : ''))

@section('content')

<div class="search-header">
    <h1 class="search-title">
        @if($query)
            Search results for <em>&ldquo;{{ $query }}&rdquo;</em>
        @else
            Search
        @endif
    </h1>

    <form class="search-form" action="/search" method="GET">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search for anything…" class="search-input" autofocus>
        <button type="submit" class="search-btn">Search</button>
    </form>
</div>

@if($query)
    @if($results->isEmpty())
        <div class="search-empty">
            <div class="search-empty-icon">&#128270;</div>
            <p>No results found for <strong>&ldquo;{{ $query }}&rdquo;</strong>.</p>
            <p style="color:#aaa;font-size:13px">Try a shorter or different keyword.</p>
        </div>
    @else
        <div class="search-meta">
            {{ $results->count() }} result{{ $results->count() !== 1 ? 's' : '' }} found
        </div>
        <div class="search-results">
            @foreach($results as $result)
                @php
                    $resultUrl = \Marble\Admin\Facades\Marble::url($result);
                    $blueprint = $result->blueprint?->name ?? 'Page';
                    // Try to get a teaser / excerpt
                    $excerpt = $result->value('teaser')
                        ?: $result->value('description')
                        ?: $result->value('intro')
                        ?: $result->value('bio');
                    if (!$excerpt) {
                        $rawContent = $result->value('content');
                        if ($rawContent) {
                            $excerpt = Str::limit(strip_tags($rawContent), 180);
                        }
                    }
                    // Highlight query in excerpt
                    if ($excerpt && $query) {
                        $highlighted = preg_replace(
                            '/(' . preg_quote($query, '/') . ')/iu',
                            '<mark>$1</mark>',
                            e(Str::limit($excerpt, 220))
                        );
                    } else {
                        $highlighted = $excerpt ? e(Str::limit($excerpt, 220)) : null;
                    }
                    // Highlight in title
                    $titleHl = $query
                        ? preg_replace('/(' . preg_quote($query, '/') . ')/iu', '<mark>$1</mark>', e($result->name()))
                        : e($result->name());
                @endphp
                <article class="search-result">
                    <div class="search-result-type">{{ $blueprint }}</div>
                    <h2 class="search-result-title">
                        <a href="{{ $resultUrl }}">{!! $titleHl !!}</a>
                    </h2>
                    @if($highlighted)
                        <p class="search-result-excerpt">{!! $highlighted !!}</p>
                    @endif
                    <div class="search-result-url">{{ $resultUrl }}</div>
                </article>
            @endforeach
        </div>
    @endif
@endif

@endsection

@push('styles')
<style>
    .search-header {
        margin-bottom: 32px;
    }
    .search-title {
        font-size: 26px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 20px;
    }
    .search-title em { font-style: normal; color: #2258A8; }

    .search-form {
        display: flex;
        gap: 10px;
        max-width: 620px;
    }
    .search-input {
        flex: 1;
        height: 46px;
        border: 2px solid #d0d8e8;
        border-radius: 8px;
        padding: 0 16px;
        font-size: 15px;
        font-family: inherit;
        color: #333;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        background: #fff;
    }
    .search-input:focus { border-color: #2258A8; box-shadow: 0 0 0 3px rgba(34,88,168,.12); }
    .search-btn {
        height: 46px;
        padding: 0 24px;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .15s;
    }
    .search-btn:hover { opacity: .9; }

    .search-meta {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
    }

    .search-results { display: flex; flex-direction: column; gap: 12px; }

    .search-result {
        background: #fff;
        border-radius: 10px;
        padding: 24px 28px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border: 1px solid #e8edf3;
        transition: box-shadow .15s, border-color .15s;
    }
    .search-result:hover { box-shadow: 0 4px 20px rgba(0,0,0,.09); border-color: #c0d0ea; }

    .search-result-type {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #2258A8;
        background: #eef3fd;
        padding: 3px 8px;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .search-result-title {
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 8px;
        line-height: 1.35;
    }
    .search-result-title a { color: #0f1a2e; }
    .search-result-title a:hover { color: #2258A8; text-decoration: none; }
    .search-result-title mark {
        background: #fff2a8;
        color: #0f1a2e;
        border-radius: 2px;
        padding: 0 1px;
    }
    .search-result-excerpt {
        font-size: 14px;
        color: #555;
        margin: 0 0 10px;
        line-height: 1.65;
    }
    .search-result-excerpt mark {
        background: #fff2a8;
        color: #333;
        border-radius: 2px;
        padding: 0 1px;
    }
    .search-result-url {
        font-size: 12px;
        color: #27ae60;
        font-family: monospace;
    }

    .search-empty {
        text-align: center;
        padding: 60px 40px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e8edf3;
    }
    .search-empty-icon { font-size: 48px; margin-bottom: 16px; }
    .search-empty p { font-size: 16px; color: #555; margin: 0 0 8px; }
</style>
@endpush
