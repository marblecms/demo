@extends('layouts.frontend')

@section('title', $item->value('name') . ' — ' . config('app.name'))

@section('content')
<div class="content-card">
    <h1>{{ $item->value('name') }}</h1>

    @if($item->value('content'))
        <div class="page-content">
            {!! $item->value('content') !!}
        </div>
    @endif

    @php $fileVal = $item->value('file'); @endphp
    @if($fileVal && !empty($fileVal['url']))
        <div class="page-attachment">
            <a href="{{ $fileVal['url'] }}" download="{{ $fileVal['original_filename'] ?? 'download' }}">
                &#128206; {{ $fileVal['original_filename'] ?? 'Download' }}
            </a>
        </div>
    @endif

    @php
        $meta = $item->value('meta');
        $children = \Marble\Admin\Facades\Marble::children($item);
    @endphp

    {{-- Child page listing (e.g. for Startpage acting as homepage) --}}
    @if($children->isNotEmpty() && !$item->value('content'))
        <div class="child-grid">
            @foreach($children as $child)
                <a href="{{ \Marble\Admin\Facades\Marble::url($child) }}" class="child-card">
                    <div class="child-card-title">{{ $child->name() }}</div>
                    <div class="child-card-arrow">→</div>
                </a>
            @endforeach
        </div>
    @endif

    @if($meta && count($meta))
        <div class="page-meta">
            <h3>Meta</h3>
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
    .page-content { line-height: 1.8; color: #444; }
    .page-content h2, .page-content h3 { color: #1a1a2e; }
    .page-content img { max-width: 100%; border-radius: 4px; }

    .page-attachment {
        margin-top: 24px; padding: 12px 16px;
        background: #f0f4fb; border: 1px solid #d0ddf0; border-radius: 4px; font-size: 14px;
    }
    .page-attachment a { font-weight: 500; }

    .child-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-top: 8px; }
    .child-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 20px; background: #f5f8ff; border: 1px solid #d0ddf0;
        border-radius: 6px; text-decoration: none; color: #2258A8;
        font-weight: 600; font-size: 15px; transition: background .15s, border-color .15s;
    }
    .child-card:hover { background: #e8eff9; border-color: #2258A8; text-decoration: none; }
    .child-card-arrow { color: #8aabdc; font-size: 18px; }

    .page-meta { margin-top: 32px; padding-top: 20px; border-top: 1px solid #eee; }
    .page-meta h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .8px; color: #999; margin: 0 0 12px; }
    .page-meta dl { display: grid; grid-template-columns: 140px 1fr; gap: 6px 16px; font-size: 13px; margin: 0; }
    .page-meta dt { color: #888; font-weight: 500; }
    .page-meta dd { margin: 0; color: #444; }
</style>
@endpush
