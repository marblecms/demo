@extends('marble::layouts.app')

@section('sidebar')
<div class="main-box clearfix profile-box-menu">
    <div class="main-box-body clearfix">
        <div class="profile-box-header gray-bg clearfix">
            <h2>Newsletter</h2>
        </div>
        <div class="profile-box-content clearfix">
            <ul class="menu-items">
                <li>
                    <a href="{{ route('marble.newsletter.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'chart_bar']) Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('marble.newsletter.subscribers.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'user']) Subscribers
                    </a>
                </li>
                <li>
                    <a href="{{ route('marble.newsletter.lists.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'group']) Lists
                    </a>
                </li>
                <li class="active">
                    <a href="{{ route('marble.newsletter.campaigns.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'email']) Campaigns
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
    $campaign = $campaign ?? null;
    $isEdit   = $campaign !== null;
    $action   = $isEdit
        ? route('marble.newsletter.campaigns.update', $campaign)
        : route('marble.newsletter.campaigns.store');
@endphp

<h1>
    @include('marble::components.famicon', ['name' => 'email'])
    {{ $isEdit ? 'Edit Campaign' : 'New Campaign' }}
</h1>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit) @method('PATCH') @endif

    <div class="main-box">
        <header class="main-box-header clearfix">
            <h2>Campaign Details</h2>
        </header>
        <div class="main-box-body clearfix">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label>Campaign Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $campaign?->name) }}" required>
                @error('name')<span class="help-block">{{ $message }}</span>@enderror
                <p class="help-block">Internal name for this campaign (not shown to subscribers).</p>
            </div>

            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                <label>Email Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control"
                       value="{{ old('subject', $campaign?->subject) }}" required>
                @error('subject')<span class="help-block">{{ $message }}</span>@enderror
            </div>

            <div class="form-group{{ $errors->has('reply_to') ? ' has-error' : '' }}">
                <label>Reply-To Email</label>
                <input type="email" name="reply_to" class="form-control"
                       value="{{ old('reply_to', $campaign?->reply_to) }}"
                       placeholder="{{ config('newsletter.from_email') }}">
                @error('reply_to')<span class="help-block">{{ $message }}</span>@enderror
            </div>

            <div class="form-group{{ $errors->has('list_id') ? ' has-error' : '' }}">
                <label>Send To List</label>
                <select name="list_id" class="form-control">
                    <option value="">— All confirmed subscribers —</option>
                    @foreach($lists as $list)
                    <option value="{{ $list->id }}"
                        {{ old('list_id', $campaign?->list_id) == $list->id ? 'selected' : '' }}>
                        {{ $list->name }}
                    </option>
                    @endforeach
                </select>
                @error('list_id')<span class="help-block">{{ $message }}</span>@enderror
                <p class="help-block">Leave blank to send to all confirmed subscribers.</p>
            </div>

            <div class="form-group{{ $errors->has('body') ? ' has-error' : '' }}">
                <label>Email Body (HTML) <span class="text-danger">*</span></label>
                <textarea name="body" class="form-control" rows="20" required>{{ old('body', $campaign?->body) }}</textarea>
                @error('body')<span class="help-block">{{ $message }}</span>@enderror
                <p class="help-block">
                    Use <code>&#123;&#123;unsubscribe_url&#125;&#125;</code> as a placeholder for the unsubscribe link.
                </p>
            </div>

        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            @include('marble::components.famicon', ['name' => 'tick'])
            {{ $isEdit ? 'Save Changes' : 'Create Campaign' }}
        </button>
        <a href="{{ route('marble.newsletter.campaigns.index') }}" class="btn btn-default">Cancel</a>
    </div>
</form>
@endsection
