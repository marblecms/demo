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
<h1>@include('marble::components.famicon', ['name' => 'email']) Campaigns</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="main-box">
    <header class="main-box-header clearfix">
        <h2 class="pull-left">All Campaigns</h2>
        <div class="pull-right">
            <a href="{{ route('marble.newsletter.campaigns.create') }}" class="btn btn-success btn-xs">
                @include('marble::components.famicon', ['name' => 'add']) New Campaign
            </a>
        </div>
        <div class="clearfix"></div>
    </header>
    <div class="main-box-body clearfix">
        @if($campaigns->isEmpty())
            <p class="text-muted text-center marble-mt-sm marble-mb-sm">No campaigns yet.</p>
        @else
        <table class="table table-hover marble-table-flush">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>List</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th class="marble-col-xs"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                <tr>
                    <td><strong>{{ $campaign->name }}</strong></td>
                    <td class="text-muted">{{ $campaign->subject }}</td>
                    <td class="text-muted marble-text-sm">{{ $campaign->list?->name ?? 'All confirmed' }}</td>
                    <td>
                        <span class="label label-{{ $campaign->status === 'sent' ? 'success' : 'default' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </td>
                    <td class="text-muted marble-text-sm">{{ $campaign->sent_at?->format('d.m.Y') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('marble.newsletter.campaigns.show', $campaign) }}" class="btn btn-info btn-xs">
                            @include('marble::components.famicon', ['name' => 'pencil'])
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="marble-mt-sm">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
