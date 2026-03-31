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

@push('styles')
<style>
    /* ── Access gate ── */
    .intranet-gate {
        max-width: 480px;
        margin: 60px auto;
        text-align: center;
        background: #fff;
        border-radius: 14px;
        padding: 56px 48px;
        box-shadow: 0 4px 24px rgba(0,0,0,.1);
        border: 1px solid #e8edf3;
    }
    .intranet-gate-icon { font-size: 52px; margin-bottom: 16px; }
    .intranet-gate-title { font-size: 26px; font-weight: 900; color: #0f1a2e; margin: 0 0 12px; }
    .intranet-gate-desc { font-size: 15px; color: #666; margin: 0 0 28px; line-height: 1.6; }
    .intranet-gate-actions { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
    .btn-gate-primary {
        display: block;
        background: linear-gradient(135deg, #2258A8, #3370cc);
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        padding: 14px 24px;
        border-radius: 7px;
        text-decoration: none;
        transition: opacity .15s;
    }
    .btn-gate-primary:hover { opacity: .9; text-decoration: none; color: #fff; }
    .btn-gate-secondary {
        display: block;
        border: 2px solid #d0d8e8;
        color: #555;
        font-weight: 600;
        font-size: 14px;
        padding: 11px 24px;
        border-radius: 7px;
        text-decoration: none;
        transition: border-color .15s;
    }
    .btn-gate-secondary:hover { border-color: #2258A8; color: #2258A8; text-decoration: none; }
    .intranet-gate-hint {
        font-size: 13px;
        color: #999;
        background: #f5f8ff;
        border-radius: 6px;
        padding: 10px 16px;
    }
    .intranet-gate-hint code {
        background: #e8edf3;
        padding: 1px 5px;
        border-radius: 3px;
        font-size: 12px;
    }

    /* ── Intranet layout ── */
    .intranet-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Sidebar */
    .intranet-sidebar {
        position: sticky;
        top: 80px;
    }
    .intranet-user-card {
        background: linear-gradient(135deg, #1a3a70, #2258A8);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .intranet-user-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(255,255,255,.25);
        color: #fff; font-weight: 800; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .intranet-user-name { font-size: 14px; font-weight: 700; color: #fff; }
    .intranet-user-label { font-size: 11px; color: rgba(255,255,255,.7); margin-top: 2px; }

    .intranet-nav {
        background: #fff;
        border-radius: 10px;
        padding: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
        margin-bottom: 12px;
    }
    .intranet-nav-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #aaa;
        padding: 4px 8px 10px;
    }
    .intranet-nav-item {
        display: block;
        padding: 9px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #2a3040;
        text-decoration: none;
        transition: background .12s;
        margin-bottom: 2px;
    }
    .intranet-nav-item:hover { background: #f0f4f8; color: #2258A8; text-decoration: none; }
    .intranet-nav-item.active { background: #e8f0fd; color: #2258A8; }
    .intranet-nav-sub { padding-left: 28px; font-size: 12px; font-weight: 500; color: #666; }
    .intranet-nav-sub.active { color: #2258A8; background: #eef3fc; }

    .intranet-signout-btn {
        width: 100%;
        background: none;
        border: 1px solid #e0e6ee;
        color: #888;
        font-size: 12px;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: background .12s, color .12s;
    }
    .intranet-signout-btn:hover { background: #fff0f0; color: #cc3333; border-color: #ffcccc; }

    /* Content area */
    .intranet-breadcrumb {
        font-size: 12px;
        color: #999;
        margin-bottom: 14px;
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .intranet-breadcrumb a { color: #2258A8; font-weight: 500; }

    .intranet-card {
        background: #fff;
        border-radius: 10px;
        padding: 36px 44px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
    }
    .intranet-card h1 {
        font-size: 26px;
        font-weight: 900;
        color: #0f1a2e;
        margin: 0 0 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e8edf3;
    }
    .intranet-body { font-size: 15px; line-height: 1.8; color: #333; }
    .intranet-body h2 { font-size: 20px; font-weight: 800; color: #0f1a2e; margin: 28px 0 12px; }
    .intranet-body h3 { font-size: 16px; font-weight: 700; margin: 22px 0 10px; }
    .intranet-body p { margin: 0 0 16px; }
    .intranet-body ul, .intranet-body ol { margin: 0 0 16px 20px; }

    .intranet-subpages { margin-top: 32px; padding-top: 24px; border-top: 1px solid #eef0f4; }
    .intranet-subpages h3 { font-size: 14px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .5px; margin: 0 0 14px; }
    .intranet-subpages-list { display: flex; flex-direction: column; gap: 8px; }
    .intranet-subpage-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f5f8ff;
        border: 1px solid #d8e4f5;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #2258A8;
        text-decoration: none;
        transition: background .12s, border-color .12s;
    }
    .intranet-subpage-link:hover { background: #e8eff9; border-color: #2258A8; text-decoration: none; }
    .intranet-subpage-arrow { color: #aac0e8; }

    @media (max-width: 768px) {
        .intranet-layout { grid-template-columns: 1fr; }
        .intranet-sidebar { position: static; }
        .intranet-card { padding: 24px 20px; }
        .intranet-gate { padding: 36px 24px; }
    }
</style>
@endpush
