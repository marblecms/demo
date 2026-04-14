@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    use Marble\Admin\Models\Item;
    use Marble\Admin\Facades\Marble;

    $version     = $item->value('version');
    $releaseDate = $item->value('release_date');
    $content     = $item->value('content');

    $parent    = $item->parent_id ? Item::find($item->parent_id) : null;
    $parentUrl = $parent ? Marble::url($parent) : '/changelog';
@endphp

<div class="blog-page-wrap">

    <x-breadcrumb :item="$item" />

    <article class="post-card">

        <div class="changelog-entry-header">
            @if($version)
                <span class="changelog-version-badge changelog-version-badge--lg">v{{ $version }}</span>
            @endif
            @if($releaseDate)
                <time class="changelog-entry-date">{{ \Carbon\Carbon::parse($releaseDate)->format('F j, Y') }}</time>
            @endif
        </div>

        <h1 class="post-title">{{ $item->name() }}</h1>

        @if($content)
            <div class="docs-body">{!! $content !!}</div>
        @endif

    </article>

    <div class="post-back">
        <a href="{{ $parentUrl }}" class="back-link">← Back to Changelog</a>
    </div>

</div>

@endsection
