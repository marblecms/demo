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
