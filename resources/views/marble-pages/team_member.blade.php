@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $role        = $item->value('role');
    $bio         = $item->value('bio');
    $linkedin    = $item->value('linkedin_url');
    $photoVal    = $item->value('photo');
    $photoUrl    = !empty($photoVal['url']) ? $photoVal['url'] : null;

    $parent    = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
    $parentUrl = $parent ? \Marble\Admin\Facades\Marble::url($parent) : null;

    // Sibling team members
    $siblings = $parent
        ? \Marble\Admin\Models\Item::where('status', 'published')
            ->where('parent_id', $parent->id)
            ->where('id', '!=', $item->id)
            ->whereHas('blueprint', fn($q) => $q->where('identifier', 'team_member'))
            ->limit(4)
            ->get()
        : collect();
@endphp

{{-- Breadcrumb --}}
<nav class="breadcrumb">
    <a href="/">Home</a>
    <span class="breadcrumb-sep">/</span>
    <a href="/about-us">About Us</a>
    @if($parent)
        <span class="breadcrumb-sep">/</span>
        <a href="{{ $parentUrl }}">{{ $parent->name() }}</a>
    @endif
    <span class="breadcrumb-sep">/</span>
    <span>{{ $item->name() }}</span>
</nav>

<div class="profile-layout">
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar-wrap">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $item->name() }}" class="profile-avatar-img">
                @else
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($item->name(), 0, 2)) }}
                    </div>
                @endif
            </div>
            <h1 class="profile-name">{{ $item->name() }}</h1>
            @if($role)
                <p class="profile-role">{{ $role }}</p>
            @endif
            @if($linkedin)
                <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="profile-linkedin">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display:inline-block;vertical-align:middle;margin-right:5px">
                        <path d="M19 3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14m-.5 15.5v-5.3a3.26 3.26 0 00-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 011.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 001.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 00-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                    </svg>
                    LinkedIn Profile
                </a>
            @endif
        </div>

        @if($siblings->isNotEmpty())
            <div class="team-sidebar">
                <h3 class="team-sidebar-title">Our Team</h3>
                @foreach($siblings as $sibling)
                    @php
                        $sibUrl  = \Marble\Admin\Facades\Marble::url($sibling);
                        $sibRole = $sibling->value('role');
                    @endphp
                    <a href="{{ $sibUrl }}" class="team-sidebar-item">
                        <div class="team-sidebar-avatar">{{ strtoupper(substr($sibling->name(), 0, 1)) }}</div>
                        <div>
                            <div class="team-sidebar-name">{{ $sibling->name() }}</div>
                            @if($sibRole)<div class="team-sidebar-role">{{ $sibRole }}</div>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="profile-main">
        <div class="profile-bio-card">
            <h2 class="profile-bio-heading">About {{ explode(' ', $item->name())[0] }}</h2>
            @if($bio)
                <div class="profile-bio-content">{!! nl2br(e($bio)) !!}</div>
            @else
                <p style="color:#999">No bio available.</p>
            @endif
        </div>
    </div>
</div>

@if($parentUrl)
    <div style="text-align:center;margin-top:20px">
        <a href="{{ $parentUrl }}" class="back-link">← Back to {{ $parent->name() }}</a>
    </div>
@endif

@endsection

@push('styles')
<style>
    .profile-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
        align-items: start;
    }

    .profile-card {
        background: #fff;
        border-radius: 10px;
        padding: 32px 24px;
        text-align: center;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
        margin-bottom: 16px;
    }
    .profile-avatar-wrap { margin-bottom: 18px; }
    .profile-avatar-img {
        width: 120px; height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e8edf3;
    }
    .profile-avatar-placeholder {
        width: 120px; height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-size: 36px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        letter-spacing: -1px;
    }
    .profile-name { font-size: 22px; font-weight: 800; color: #0f1a2e; margin: 0 0 6px; }
    .profile-role { font-size: 13px; color: #2258A8; font-weight: 600; margin: 0 0 16px; text-transform: uppercase; letter-spacing: .5px; }
    .profile-linkedin {
        display: inline-flex;
        align-items: center;
        font-size: 13px;
        color: #0077b5;
        font-weight: 600;
        text-decoration: none;
        padding: 7px 14px;
        border: 1px solid #0077b5;
        border-radius: 20px;
        transition: background .15s;
    }
    .profile-linkedin:hover { background: #e8f4fd; text-decoration: none; }

    .team-sidebar {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
    }
    .team-sidebar-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #999; margin: 0 0 14px; }
    .team-sidebar-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f0f4f8;
        text-decoration: none;
    }
    .team-sidebar-item:last-child { border-bottom: none; }
    .team-sidebar-item:hover { text-decoration: none; }
    .team-sidebar-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, #2258A8, #5588cc);
        color: #fff; font-size: 13px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .team-sidebar-name { font-size: 13px; font-weight: 700; color: #0f1a2e; }
    .team-sidebar-name:hover { color: #2258A8; }
    .team-sidebar-role { font-size: 11px; color: #999; }

    .profile-bio-card {
        background: #fff;
        border-radius: 10px;
        padding: 36px 40px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
    }
    .profile-bio-heading {
        font-size: 20px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 20px;
        padding-bottom: 14px;
        border-bottom: 2px solid #e8edf3;
    }
    .profile-bio-content { font-size: 15px; line-height: 1.85; color: #333; }

    .back-link { font-size: 13px; font-weight: 600; color: #2258A8; }
    .back-link:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .profile-layout { grid-template-columns: 1fr; }
        .profile-bio-card { padding: 24px 20px; }
    }
</style>
@endpush
