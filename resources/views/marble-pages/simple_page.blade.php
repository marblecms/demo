@extends('layouts.frontend')

@section('title', $item->value('name') ?: $item->name())

@section('content')

@php
    $name     = $item->value('name') ?: $item->name();
    $content  = $item->value('content');
    $fileVal  = $item->value('file');
    $meta     = $item->value('meta');
    $children = \Marble\Admin\Facades\Marble::children($item);
@endphp

<div class="simple-page-header">
    <div class="wrap">
        <x-breadcrumb :item="$item" />
        <h1 class="simple-page-title">{{ $name }}</h1>
    </div>
</div>

<div class="simple-page-body">
    <div class="wrap">
        @if($content)
            <div class="simple-page-content">{!! $content !!}</div>
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
</div>

@endsection
