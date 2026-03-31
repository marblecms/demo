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

@push('styles')
<style>
    .page-content { line-height: 1.85; color: #333; font-size: 15px; }
    .page-content h2 { font-size: 22px; font-weight: 800; color: #0f1a2e; margin: 32px 0 12px; }
    .page-content h3 { font-size: 18px; font-weight: 700; color: #1a2e50; margin: 24px 0 10px; }
    .page-content p { margin: 0 0 16px; }
    .page-content ul, .page-content ol { margin: 0 0 16px 20px; }
    .page-content img { max-width: 100%; border-radius: 6px; }
    .page-content a { color: #2258A8; font-weight: 500; }
    .page-content blockquote {
        border-left: 4px solid #2258A8;
        margin: 20px 0;
        padding: 12px 20px;
        background: #f5f8ff;
        color: #445;
        border-radius: 0 6px 6px 0;
    }

    .page-attachment {
        margin-top: 24px;
        padding: 13px 18px;
        background: #f0f4fb;
        border: 1px solid #d0ddf0;
        border-radius: 6px;
        font-size: 14px;
    }
    .page-attachment a { font-weight: 600; color: #2258A8; }

    .child-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 14px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #eef0f4;
    }
    .child-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #f5f8ff;
        border: 1px solid #d0ddf0;
        border-radius: 8px;
        color: #2258A8;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: background .12s, border-color .12s;
    }
    .child-card:hover { background: #e8eff9; border-color: #2258A8; text-decoration: none; }
    .child-card-arrow { color: #aac0e8; font-size: 18px; }

    .page-meta { margin-top: 32px; padding-top: 20px; border-top: 1px solid #eef0f4; }
    .page-meta h3 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #aaa; margin: 0 0 12px; }
    .page-meta dl { display: grid; grid-template-columns: 140px 1fr; gap: 6px 16px; font-size: 14px; margin: 0; }
    .page-meta dt { color: #888; font-weight: 600; }
    .page-meta dd { margin: 0; color: #444; }
</style>
@endpush
