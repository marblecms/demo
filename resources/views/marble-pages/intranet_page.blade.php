@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $isPortalAuth = \Marble\Admin\Facades\Marble::isPortalAuthenticated();
    $portalUser   = \Marble\Admin\Facades\Marble::portalUser();

    if (!$isPortalAuth) {
        // Show a teaser / login prompt instead of content
        $showGate = true;
    } else {
        $showGate    = false;
        $content     = $item->value('content');

        // Build intranet sidebar tree: get top-level intranet item and its tree
        // Find root intranet item (this item or its ancestor with intranet_page blueprint)
        $intranetRoot = null;
        $checkItem    = $item;
        while ($checkItem) {
            if ($checkItem->blueprint?->identifier === 'intranet_page') {
                $intranetRoot = $checkItem;
            }
            $checkItem = $checkItem->parent_id ? \Marble\Admin\Models\Item::find($checkItem->parent_id) : null;
        }

        // Get intranet sidebar (children of root or top-level intranet items)
        $sidebarItems = collect();
        if ($intranetRoot) {
            $sidebarItems = \Marble\Admin\Models\Item::where('status', 'published')
                ->where('parent_id', $intranetRoot->id)
                ->whereHas('blueprint', fn($q) => $q->where('identifier', 'intranet_page'))
                ->get();
        }
    }
@endphp

@if($showGate)
    {{-- ── Access Gate ──────────────────────────────────────────────── --}}
    <div class="intranet-gate">
        <div class="intranet-gate-icon">&#128274;</div>
        <h1 class="intranet-gate-title">Members Only</h1>
        <p class="intranet-gate-desc">
            This section is restricted to registered members.<br>
            Please sign in to access this content.
        </p>
        <div class="intranet-gate-actions">
            <a href="{{ route('marble.portal.login') }}?redirect={{ urlencode(request()->url()) }}" class="btn-gate-primary">
                Sign in to your account
            </a>
            <a href="/contact" class="btn-gate-secondary">Request access</a>
        </div>
        <div class="intranet-gate-hint">
            <strong>Demo:</strong> Sign in with <code>demo@demo.com</code> / <code>demo</code>
        </div>
    </div>
@else
    {{-- ── Authenticated Content ─────────────────────────────────────── --}}
    <div class="intranet-layout">
        <aside class="intranet-sidebar">
            <div class="intranet-user-card">
                <div class="intranet-user-avatar">{{ strtoupper(substr($portalUser->email, 0, 1)) }}</div>
                <div>
                    <div class="intranet-user-name">{{ explode('@', $portalUser->email)[0] }}</div>
                    <div class="intranet-user-label">Intranet Member</div>
                </div>
            </div>

            <nav class="intranet-nav">
                <div class="intranet-nav-title">Intranet</div>
                @if($intranetRoot)
                    <a href="{{ \Marble\Admin\Facades\Marble::url($intranetRoot) }}"
                       class="intranet-nav-item {{ $item->id === $intranetRoot->id ? 'active' : '' }}">
                        &#127968; {{ $intranetRoot->name() }}
                    </a>
                @endif
                @foreach($sidebarItems as $sidebarItem)
                    @php
                        $sidebarUrl  = \Marble\Admin\Facades\Marble::url($sidebarItem);
                        $isActive    = $item->id === $sidebarItem->id || str_starts_with($item->path, $sidebarItem->path);
                        $subChildren = \Marble\Admin\Models\Item::where('status', 'published')
                            ->where('parent_id', $sidebarItem->id)
                            ->whereHas('blueprint', fn($q) => $q->where('identifier', 'intranet_page'))
                            ->get();
                    @endphp
                    <a href="{{ $sidebarUrl }}"
                       class="intranet-nav-item {{ $isActive ? 'active' : '' }}">
                        &#128196; {{ $sidebarItem->name() }}
                    </a>
                    @if($isActive && $subChildren->isNotEmpty())
                        @foreach($subChildren as $sub)
                            <a href="{{ \Marble\Admin\Facades\Marble::url($sub) }}"
                               class="intranet-nav-item intranet-nav-sub {{ $item->id === $sub->id ? 'active' : '' }}">
                                ↳ {{ $sub->name() }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </nav>

            <form method="POST" action="{{ route('marble.portal.logout') }}" style="margin-top:16px">
                @csrf
                <button type="submit" class="intranet-signout-btn">Sign out</button>
            </form>
        </aside>

        <div class="intranet-content">
            <div class="intranet-breadcrumb">
                @if($intranetRoot && $item->id !== $intranetRoot->id)
                    <a href="{{ \Marble\Admin\Facades\Marble::url($intranetRoot) }}">{{ $intranetRoot->name() }}</a>
                    <span class="breadcrumb-sep">/</span>
                @endif
                <span>{{ $item->name() }}</span>
            </div>

            <div class="intranet-card">
                <h1>{{ $item->name() }}</h1>
                @if($content)
                    <div class="intranet-body">{!! $content !!}</div>
                @else
                    <p style="color:#999">No content has been added to this page yet.</p>
                @endif

                {{-- Show non-intranet children (e.g. documents) --}}
                @php
                    $subPages = \Marble\Admin\Models\Item::where('status', 'published')
                        ->where('parent_id', $item->id)
                        ->whereHas('blueprint', fn($q) => $q->where('identifier', 'intranet_page'))
                        ->get();
                @endphp
                @if($subPages->isNotEmpty())
                    <div class="intranet-subpages">
                        <h3>Sub-pages</h3>
                        <div class="intranet-subpages-list">
                            @foreach($subPages as $sub)
                                <a href="{{ \Marble\Admin\Facades\Marble::url($sub) }}" class="intranet-subpage-link">
                                    &#128196; {{ $sub->name() }}
                                    <span class="intranet-subpage-arrow">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

@endsection
