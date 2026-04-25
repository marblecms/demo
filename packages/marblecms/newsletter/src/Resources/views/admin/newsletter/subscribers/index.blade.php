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
                <li class="active">
                    <a href="{{ route('marble.newsletter.subscribers.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'user']) Subscribers
                    </a>
                </li>
                <li>
                    <a href="{{ route('marble.newsletter.lists.index') }}" class="clearfix">
                        @include('marble::components.famicon', ['name' => 'group']) Lists
                    </a>
                </li>
                <li>
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
<h1>@include('marble::components.famicon', ['name' => 'user']) Subscribers</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="main-box">
    <header class="main-box-header clearfix">
        <h2 class="pull-left">All Subscribers</h2>
        <div class="clearfix"></div>
    </header>
    <div class="main-box-body clearfix">
        @if($subscribers->isEmpty())
            <p class="text-muted text-center marble-mt-sm marble-mb-sm">No subscribers yet.</p>
        @else
        <table class="table table-hover marble-table-flush">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Lists</th>
                    <th>Subscribed</th>
                    <th class="marble-col-xs"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscribers as $sub)
                <tr>
                    <td><strong>{{ $sub->email }}</strong></td>
                    <td class="text-muted">{{ $sub->name ?? '—' }}</td>
                    <td>
                        <span class="label label-{{ match($sub->status) {
                            'confirmed'    => 'success',
                            'unsubscribed' => 'danger',
                            default        => 'default',
                        } }}">{{ ucfirst($sub->status) }}</span>
                    </td>
                    <td class="text-muted marble-text-sm">
                        {{ $sub->lists->pluck('name')->join(', ') ?: '—' }}
                    </td>
                    <td class="text-muted marble-text-sm">{{ $sub->created_at->format('d.m.Y') }}</td>
                    <td>
                        <form method="POST"
                              action="{{ route('marble.newsletter.subscribers.destroy', $sub) }}"
                              onsubmit="return confirm('Delete subscriber?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">
                                @include('marble::components.famicon', ['name' => 'delete'])
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="marble-mt-sm">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
