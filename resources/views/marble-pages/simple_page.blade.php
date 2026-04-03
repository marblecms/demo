@extends('layouts.frontend')

@section('title', $item->value('name') ?: $item->name())

@section('content')

@php
    $name     = $item->value('name') ?: $item->name();
    $content  = $item->value('content');
    $fileVal  = $item->value('file');
    $meta     = $item->value('meta');
    $children = \Marble\Admin\Facades\Marble::children($item);

    // Breadcrumb ancestors
    $ancestors = collect();
    $check = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
    while ($check) {
        $ancestors->prepend($check);
        $check = $check->parent_id ? \Marble\Admin\Models\Item::find($check->parent_id) : null;
    }
@endphp

{{-- Breadcrumb --}}
@if($ancestors->isNotEmpty())
    <nav class="breadcrumb">
        <a href="/">Home</a>
        @foreach($ancestors as $anc)
            <span class="breadcrumb-sep">/</span>
            <a href="{{ \Marble\Admin\Facades\Marble::url($anc) }}">{{ $anc->name() }}</a>
        @endforeach
        <span class="breadcrumb-sep">/</span>
        <span>{{ $name }}</span>
    </nav>
@endif

<div class="content-card">
    <h1>{{ $name }}</h1>

    @if($content)
        <div class="page-content">{!! $content !!}</div>
    @endif

    @if($fileVal && !empty($fileVal['url']))
        <div class="page-attachment">
            <a href="{{ $fileVal['url'] }}" download="{{ $fileVal['original_filename'] ?? 'download' }}">
                &#128206; {{ $fileVal['original_filename'] ?? 'Download' }}
            </a>
        </div>
    @endif

    @if($children->isNotEmpty())
        <div class="child-grid">
            @foreach($children as $child)
                <a href="{{ \Marble\Admin\Facades\Marble::url($child) }}" class="child-card">
                    <div class="child-card-title">{{ $child->name() }}</div>
                    <div class="child-card-arrow">→</div>
                </a>
            @endforeach
        </div>
    @endif

    @if($meta && count(array_filter($meta, fn($r) => !empty($r['key']))))
        <div class="page-meta">
            <h3>Additional Info</h3>
            <dl>
                @foreach($meta as $row)
                    @if(!empty($row['key']))
                        <dt>{{ $row['key'] }}</dt>
                        <dd>{{ $row['value'] ?? '' }}</dd>
                    @endif
                @endforeach
            </dl>
        </div>
    @endif
</div>

@endsection
