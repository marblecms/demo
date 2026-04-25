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
<h1>
    @include('marble::components.famicon', ['name' => 'email'])
    {{ $campaign->name }}
    <span class="label label-{{ $campaign->status === 'sent' ? 'success' : 'default' }} marble-ml-xs">
        {{ ucfirst($campaign->status) }}
    </span>
</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Stats --}}
@if($campaign->status === 'sent')
<div class="row marble-mb-sm">
    <div class="col-sm-4">
        <div class="main-box">
            <div class="main-box-body clearfix">
                <p class="marble-stat-number">{{ $sentCount }}</p>
                <p class="text-muted marble-text-sm marble-mb-0">
                    @include('marble::components.famicon', ['name' => 'email']) Delivered
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="main-box">
            <div class="main-box-body clearfix">
                <p class="marble-stat-number">{{ $openCount }}</p>
                <p class="text-muted marble-text-sm marble-mb-0">
                    @include('marble::components.famicon', ['name' => 'eye']) Opens
                </p>
                @if($sentCount > 0)
                <p class="text-muted marble-text-sm marble-mb-0">
                    {{ number_format(($openCount / $sentCount) * 100, 1) }}% open rate
                </p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="main-box">
            <div class="main-box-body clearfix">
                <p class="marble-stat-number">{{ $clickCount }}</p>
                <p class="text-muted marble-text-sm marble-mb-0">
                    @include('marble::components.famicon', ['name' => 'link']) Clicks
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="main-box">
    <header class="main-box-header clearfix">
        <h2 class="pull-left">Details</h2>
        <div class="pull-right">
            @if($campaign->status === 'draft')
            <a href="{{ route('marble.newsletter.campaigns.create') }}?edit={{ $campaign->id }}" class="btn btn-default btn-xs">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('marble.newsletter.campaigns.send', $campaign) }}"
                  class="pull-right marble-ml-xs"
                  onsubmit="return confirm('Send this campaign now? This cannot be undone.')">
                @csrf
                <button type="submit" class="btn btn-success btn-xs">
                    @include('marble::components.famicon', ['name' => 'email']) Send Now
                </button>
            </form>
            @endif
            <form method="POST"
                  action="{{ route('marble.newsletter.campaigns.destroy', $campaign) }}"
                  class="pull-right marble-ml-xs"
                  onsubmit="return confirm('Delete this campaign?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs">
                    @include('marble::components.famicon', ['name' => 'delete'])
                </button>
            </form>
        </div>
        <div class="clearfix"></div>
    </header>
    <div class="main-box-body clearfix">
        <table class="table table-condensed">
            <tr><th class="marble-col-sm">Subject</th><td>{{ $campaign->subject }}</td></tr>
            <tr><th>Reply-To</th><td>{{ $campaign->reply_to ?? config('newsletter.from_email') }}</td></tr>
            <tr><th>List</th><td>{{ $campaign->list?->name ?? 'All confirmed subscribers' }}</td></tr>
            @if($campaign->sent_at)
            <tr><th>Sent At</th><td>{{ $campaign->sent_at->format('d.m.Y H:i') }}</td></tr>
            @endif
        </table>
    </div>
</div>

@if($campaign->status === 'draft')
<div class="main-box">
    <header class="main-box-header clearfix">
        <h2>Edit Campaign</h2>
    </header>
    <div class="main-box-body clearfix">
        @php $lists = \MarbleCms\Newsletter\Models\SubscriberList::orderBy('name')->get(); @endphp
        <form method="POST" action="{{ route('marble.newsletter.campaigns.update', $campaign) }}">
            @csrf
            @method('PATCH')

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label>Campaign Name</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $campaign->name) }}" required>
                @error('name')<span class="help-block">{{ $message }}</span>@enderror
            </div>

            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                <label>Email Subject</label>
                <input type="text" name="subject" class="form-control"
                       value="{{ old('subject', $campaign->subject) }}" required>
                @error('subject')<span class="help-block">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Reply-To Email</label>
                <input type="email" name="reply_to" class="form-control"
                       value="{{ old('reply_to', $campaign->reply_to) }}"
                       placeholder="{{ config('newsletter.from_email') }}">
            </div>

            <div class="form-group">
                <label>Send To List</label>
                <select name="list_id" class="form-control">
                    <option value="">— All confirmed subscribers —</option>
                    @foreach($lists as $list)
                    <option value="{{ $list->id }}"
                        {{ old('list_id', $campaign->list_id) == $list->id ? 'selected' : '' }}>
                        {{ $list->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group{{ $errors->has('body') ? ' has-error' : '' }}">
                <label>Email Body (HTML)</label>
                <textarea name="body" class="form-control" rows="20" required>{{ old('body', $campaign->body) }}</textarea>
                @error('body')<span class="help-block">{{ $message }}</span>@enderror
                <p class="help-block">
                    Use <code>&#123;&#123;unsubscribe_url&#125;&#125;</code> for the unsubscribe link.
                </p>
            </div>

            <button type="submit" class="btn btn-primary">
                @include('marble::components.famicon', ['name' => 'tick']) Save Changes
            </button>
        </form>
    </div>
</div>
@endif
@endsection
