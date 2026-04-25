@extends('marble::layouts.app')

@section('sidebar')
<div class="main-box clearfix profile-box-menu">
    <div class="main-box-body clearfix">
        <div class="profile-box-header gray-bg clearfix">
            <h2>SEO</h2>
        </div>
        <div class="profile-box-content clearfix">
            <ul class="menu-items">
                <li>
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
<h1>
    @include('marble::components.famicon', ['name' => 'page_world'])
    SEO: {{ $item->name() }}
</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('marble.seo.update', $item) }}">
    @csrf

    {{-- Language tabs --}}
    <ul class="nav nav-tabs marble-mb-sm" role="tablist">
        @foreach($languages as $index => $lang)
        <li role="presentation" class="{{ $index === 0 ? 'active' : '' }}">
            <a href="#lang-tab-{{ $lang->id }}" aria-controls="lang-tab-{{ $lang->id }}" role="tab" data-toggle="tab">
                {{ $lang->name ?? strtoupper($lang->code ?? $lang->locale) }}
            </a>
        </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($languages as $index => $lang)
        @php $meta = $metas->get($lang->id); @endphp
        <div role="tabpanel" class="tab-pane {{ $index === 0 ? 'active' : '' }}" id="lang-tab-{{ $lang->id }}">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <h2>{{ $lang->name ?? strtoupper($lang->code ?? $lang->locale) }}</h2>
                </header>
                <div class="main-box-body clearfix">

                    <div class="form-group">
                        <label for="title_{{ $lang->id }}">SEO Title</label>
                        <input type="text"
                               class="form-control"
                               id="title_{{ $lang->id }}"
                               name="lang[{{ $lang->id }}][title]"
                               value="{{ old("lang.{$lang->id}.title", $meta?->title) }}"
                               placeholder="{{ $item->name($lang->id) }}">
                        <p class="help-block">Overrides the item name in search results. Leave blank to use the item name.</p>
                    </div>

                    <div class="form-group">
                        <label for="desc_{{ $lang->id }}">Meta Description</label>
                        <textarea class="form-control"
                                  id="desc_{{ $lang->id }}"
                                  name="lang[{{ $lang->id }}][description]"
                                  rows="3"
                                  maxlength="500">{{ old("lang.{$lang->id}.description", $meta?->description) }}</textarea>
                        <p class="help-block">Maximum 500 characters. Used in search snippets and og:description.</p>
                    </div>

                    <div class="form-group">
                        <label for="og_image_{{ $lang->id }}">OG Image URL</label>
                        <input type="url"
                               class="form-control"
                               id="og_image_{{ $lang->id }}"
                               name="lang[{{ $lang->id }}][og_image_url]"
                               value="{{ old("lang.{$lang->id}.og_image_url", $meta?->og_image_url) }}"
                               placeholder="{{ config('seo.og_default_image') }}">
                        <p class="help-block">URL of the image to use for social sharing. Leave blank to use the site default.</p>
                    </div>

                    <div class="form-group">
                        <label for="canonical_{{ $lang->id }}">Canonical URL</label>
                        <input type="url"
                               class="form-control"
                               id="canonical_{{ $lang->id }}"
                               name="lang[{{ $lang->id }}][canonical_url]"
                               value="{{ old("lang.{$lang->id}.canonical_url", $meta?->canonical_url) }}">
                        <p class="help-block">Override the canonical URL. Leave blank to use the item's default URL.</p>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="hidden"  name="lang[{{ $lang->id }}][noindex]" value="0">
                                <input type="checkbox"
                                       name="lang[{{ $lang->id }}][noindex]"
                                       value="1"
                                       {{ old("lang.{$lang->id}.noindex", $meta?->noindex) ? 'checked' : '' }}>
                                No-index this page (hide from search engines)
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            @include('marble::components.famicon', ['name' => 'tick']) Save SEO
        </button>
        <a href="{{ route('marble.seo.index') }}" class="btn btn-default">Cancel</a>
    </div>
</form>
@endsection
