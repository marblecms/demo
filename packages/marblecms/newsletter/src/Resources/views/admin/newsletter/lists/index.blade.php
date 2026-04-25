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
                <li class="active">
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
<h1>@include('marble::components.famicon', ['name' => 'group']) Lists</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <h2 class="pull-left">All Lists</h2>
                <div class="clearfix"></div>
            </header>
            <div class="main-box-body clearfix">
                @if($lists->isEmpty())
                    <p class="text-muted text-center marble-mt-sm marble-mb-sm">No lists yet.</p>
                @else
                <table class="table table-hover marble-table-flush">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="marble-col-sm">Subscribers</th>
                            <th class="marble-col-xs"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lists as $list)
                        <tr>
                            <td><strong>{{ $list->name }}</strong></td>
                            <td class="text-muted marble-text-sm">{{ $list->description ?? '—' }}</td>
                            <td class="text-center">{{ $list->subscribers_count }}</td>
                            <td>
                                <form method="POST"
                                      action="{{ route('marble.newsletter.lists.destroy', $list) }}"
                                      onsubmit="return confirm('Delete list?')">
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
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <h2>@include('marble::components.famicon', ['name' => 'add']) New List</h2>
            </header>
            <div class="main-box-body clearfix">
                <form method="POST" action="{{ route('marble.newsletter.lists.store') }}">
                    @csrf
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                        @error('name')<span class="help-block">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        @include('marble::components.famicon', ['name' => 'tick']) Create List
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
