@extends('marble::layouts.app')

@section('sidebar')
<div class="main-box clearfix profile-box-menu">
    <div class="main-box-body clearfix">
        <div class="profile-box-header gray-bg clearfix">
            <h2>SEO</h2>
        </div>
        <div class="profile-box-content clearfix">
            <ul class="menu-items">
                <li class="active">
                    <a href="{{ route('marble.seo.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'page_world']) All Items
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')
<h1>@include('marble::components.famicon', ['name' => 'page_world']) SEO</h1>

<div class="main-box">
    <header class="main-box-header clearfix">
        <h2 class="pull-left">Published Items</h2>
        <div class="clearfix"></div>
    </header>
    <div class="main-box-body clearfix">
        @if($items->isEmpty())
            <p class="text-muted text-center marble-mt-sm marble-mb-sm">No published items found.</p>
        @else
        <table class="table table-hover marble-table-flush">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Blueprint</th>
                    @foreach($languages as $lang)
                    <th class="text-center">{{ strtoupper($lang->code ?? $lang->locale) }}</th>
                    @endforeach
                    <th class="marble-col-xs"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                @php
                    $itemMetas = ($allMetas->get($item->id) ?? collect())->keyBy('language_id');
                @endphp
                <tr>
                    <td>
                        <strong>{{ $item->name() }}</strong>
                        @if($item->path)
                        <br><small class="text-muted">{{ $item->path }}</small>
                        @endif
                    </td>
                    <td class="text-muted marble-text-sm">{{ $item->blueprint?->name ?? '—' }}</td>
                    @foreach($languages as $lang)
                    @php $langMeta = $itemMetas->get($lang->id); @endphp
                    <td class="text-center">
                        @if($langMeta && ($langMeta->title || $langMeta->description))
                            <span class="label label-success">
                                @include('marble::components.famicon', ['name' => 'tick'])
                            </span>
                        @else
                            <span class="label label-default">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td>
                        <a href="{{ route('marble.seo.edit', $item) }}" class="btn btn-info btn-xs">
                            @include('marble::components.famicon', ['name' => 'pencil'])
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="marble-mt-sm">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
