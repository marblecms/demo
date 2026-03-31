@php
    $settings    = \Marble\Admin\Facades\Marble::settings();
    $siteName    = $settings?->value('site_name') ?: config('app.name', 'Marble');
    $tagline     = $settings?->value('tagline');
    $copyright   = $settings?->value('copyright');
    $metaDesc    = $settings?->value('meta_description');
    $ogImage     = $settings?->value('og_image');
    $robots      = $settings?->value('robots') ?: 'index, follow';
    $instagram   = $settings?->value('instagram_url');
    $facebook    = $settings?->value('facebook_url');
    $linkedin    = $settings?->value('linkedin_url');

    $titleTemplate = $settings?->value('meta_title_template') ?: '%title% | ' . $siteName;
    $pageTitle     = str_replace('%title%', $__env->yieldContent('title', $siteName), $titleTemplate);
    if ($__env->yieldContent('title') === '') {
        $pageTitle = $siteName . ($tagline ? ' — ' . $tagline : '');
    }

    $logoVal = $settings?->value('logo');
    $logoUrl = !empty($logoVal['url']) ? $logoVal['url'] : null;

    $navItems      = \Marble\Admin\Facades\Marble::navigation(null, 3);
    $currentPath   = '/' . ltrim(request()->path(), '/');
    $portalUser    = \Marble\Admin\Facades\Marble::portalUser();
    $isPortalAuth  = \Marble\Admin\Facades\Marble::isPortalAuthenticated();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>

    @if($metaDesc)
        <meta name="description" content="{{ $metaDesc }}">
    @endif
    <meta name="robots" content="{{ $robots }}">

    @if($ogImage && !empty($ogImage['url']))
        <meta property="og:image" content="{{ $ogImage['url'] }}">
    @endif
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($metaDesc)
        <meta property="og:description" content="{{ $metaDesc }}">
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            background: #f0f4f8;
            color: #333;
            font-size: 15px;
            line-height: 1.7;
        }
        a { color: #2258A8; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* ── Header ─────────────────────────────── */
        .site-header {
            background: linear-gradient(135deg, #1a3a70 0%, #2258A8 60%, #3370cc 100%);
            color: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .site-header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            height: 60px;
        }
        .site-logo {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.3px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .site-logo:hover { text-decoration: none; color: #fff; }
        .site-logo img { height: 30px; width: auto; display: block; }
        .site-logo-mark {
            width: 32px; height: 32px; border-radius: 7px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900; letter-spacing: -1px;
            border: 1px solid rgba(255,255,255,.3);
        }

        /* ── Dropdown Navigation ─────────────────── */
        .site-nav {
            flex: 1;
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2px;
            align-items: center;
        }
        .site-nav > li {
            position: relative;
        }
        .site-nav > li > a {
            display: flex;
            align-items: center;
            gap: 5px;
            color: rgba(255,255,255,.88);
            padding: 8px 13px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            transition: background .15s, color .15s;
        }
        .site-nav > li > a:hover,
        .site-nav > li:hover > a { background: rgba(255,255,255,.18); color: #fff; text-decoration: none; }
        .site-nav > li > a.active { background: rgba(255,255,255,.22); color: #fff; font-weight: 700; }
        .site-nav > li > a .nav-arrow {
            font-size: 10px;
            opacity: .7;
            transition: transform .15s;
        }
        .site-nav > li:hover > a .nav-arrow { transform: rotate(180deg); }

        /* Level 2 dropdown */
        .site-nav .dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            background: #fff;
            min-width: 210px;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            padding: 6px 0;
            z-index: 999;
            border: 1px solid rgba(0,0,0,.07);
        }
        .site-nav > li:hover > .dropdown { display: block; }
        .site-nav .dropdown li {
            position: relative;
            list-style: none;
        }
        .site-nav .dropdown li > a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 18px;
            color: #2a3040;
            font-size: 14px;
            font-weight: 500;
            transition: background .12s;
            white-space: nowrap;
        }
        .site-nav .dropdown li > a:hover { background: #f0f4f8; text-decoration: none; color: #2258A8; }
        .site-nav .dropdown li > a .sub-arrow { font-size: 11px; opacity: .5; }

        /* Level 3 dropdown */
        .site-nav .dropdown .dropdown {
            top: -6px;
            left: 100%;
        }
        .site-nav .dropdown li:hover > .dropdown { display: block; }

        /* ── Header right: search + portal ──────── */
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .header-search {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 20px;
            padding: 0 12px;
            height: 34px;
            transition: background .15s, border-color .15s;
        }
        .header-search:focus-within {
            background: rgba(255,255,255,.25);
            border-color: rgba(255,255,255,.5);
        }
        .header-search input {
            background: none;
            border: none;
            outline: none;
            color: #fff;
            font-size: 13px;
            width: 140px;
            placeholder-color: rgba(255,255,255,.6);
        }
        .header-search input::placeholder { color: rgba(255,255,255,.65); }
        .header-search button {
            background: none;
            border: none;
            color: rgba(255,255,255,.75);
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .header-search button:hover { color: #fff; }

        .portal-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .portal-badge {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 20px;
            padding: 4px 12px 4px 8px;
            font-size: 12px;
            color: rgba(255,255,255,.9);
            font-weight: 600;
        }
        .portal-badge-avatar {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(255,255,255,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }
        .portal-login-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            color: rgba(255,255,255,.85);
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
        }
        .portal-login-btn:hover { background: rgba(255,255,255,.22); color: #fff; text-decoration: none; }

        /* ── Main Content ────────────────────────── */
        .site-main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 24px;
        }

        /* ── Generic content card ────────────────── */
        .content-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            padding: 40px 48px;
        }
        .content-card h1 {
            margin: 0 0 28px;
            font-size: 28px;
            font-weight: 800;
            color: #0f1a2e;
            border-bottom: 2px solid #e8edf3;
            padding-bottom: 18px;
        }

        /* ── Breadcrumb ──────────────────────────── */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #999;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .breadcrumb a { color: #2258A8; font-weight: 500; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb-sep { color: #ccc; }

        /* ── Page section titles ─────────────────── */
        .section-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f1a2e;
            margin: 0 0 6px;
        }
        .section-sub {
            font-size: 15px;
            color: #666;
            margin: 0 0 28px;
        }

        /* ── Card grid ───────────────────────────── */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .card-grid-sm {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }

        /* ── Footer ──────────────────────────────── */
        .site-footer {
            background: #0f1a2e;
            color: #8899aa;
            font-size: 13px;
            padding: 40px 24px 28px;
            margin-top: 60px;
        }
        .site-footer-inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .site-footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .site-footer-brand .footer-logo {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
        }
        .site-footer-brand p { margin: 0; line-height: 1.6; }
        .site-footer-col h4 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            margin: 0 0 14px;
        }
        .site-footer-col ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .site-footer-col ul li a {
            color: #8899aa;
            font-size: 13px;
            transition: color .15s;
        }
        .site-footer-col ul li a:hover { color: #fff; text-decoration: none; }
        .site-footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08);
            padding-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .site-footer-social { display: flex; gap: 12px; }
        .site-footer-social a {
            color: #8899aa;
            font-size: 12px;
            transition: color .15s;
        }
        .site-footer-social a:hover { color: #fff; text-decoration: none; }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 768px) {
            .site-header-inner { height: auto; flex-wrap: wrap; padding: 12px 16px; gap: 12px; }
            .site-nav { flex-wrap: wrap; }
            .header-search input { width: 90px; }
            .site-main { margin: 24px auto; }
            .content-card { padding: 24px 20px; }
            .site-footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .site-footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
    @stack('styles')
</head>
<body>

<header class="site-header">
    <div class="site-header-inner">
        <a href="/" class="site-logo">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
            @else
                <span class="site-logo-mark">M</span>
                {{ $siteName }}
            @endif
        </a>

        <nav>
            <ul class="site-nav">
                @foreach($navItems as $navItem)
                    @php
                        $navUrl  = \Marble\Admin\Facades\Marble::url($navItem);
                        $isIntranet = $navItem->blueprint?->identifier === 'intranet_page';
                        $hasChildren = $navItem->_children && $navItem->_children->isNotEmpty();
                    @endphp
                    @if($isIntranet && !$isPortalAuth)
                        {{-- Show intranet link only when logged in --}}
                        @continue
                    @endif
                    <li>
                        <a href="{{ $navUrl }}"
                           class="{{ $currentPath === $navUrl || str_starts_with($currentPath, rtrim($navUrl, '/') . '/') ? 'active' : '' }}">
                            {{ $navItem->name() }}
                            @if($hasChildren)
                                <span class="nav-arrow">▾</span>
                            @endif
                        </a>
                        @if($hasChildren)
                            <ul class="dropdown">
                                @foreach($navItem->_children as $child)
                                    @php
                                        $childUrl      = \Marble\Admin\Facades\Marble::url($child);
                                        $hasGrandchildren = $child->_children && $child->_children->isNotEmpty();
                                    @endphp
                                    <li>
                                        <a href="{{ $childUrl }}">
                                            {{ $child->name() }}
                                            @if($hasGrandchildren)
                                                <span class="sub-arrow">›</span>
                                            @endif
                                        </a>
                                        @if($hasGrandchildren)
                                            <ul class="dropdown">
                                                @foreach($child->_children as $grandchild)
                                                    <li>
                                                        <a href="{{ \Marble\Admin\Facades\Marble::url($grandchild) }}">
                                                            {{ $grandchild->name() }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="header-right">
            <form class="header-search" action="/search" method="GET">
                <button type="submit" title="Search">&#128269;</button>
                <input type="text" name="q" placeholder="Search…"
                       value="{{ request('q') }}"
                       autocomplete="off">
            </form>

            <div class="portal-indicator">
                @if($isPortalAuth && $portalUser)
                    <span class="portal-badge">
                        <span class="portal-badge-avatar">{{ strtoupper(substr($portalUser->email, 0, 1)) }}</span>
                        {{ explode('@', $portalUser->email)[0] }}
                    </span>
                    <form method="POST" action="{{ route('marble.portal.logout') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="portal-login-btn" style="background:rgba(255,60,60,.2);border-color:rgba(255,100,100,.3)">
                            Sign out
                        </button>
                    </form>
                @else
                    <a href="{{ route('marble.portal.login') }}" class="portal-login-btn">
                        &#128100; Sign in
                    </a>
                @endif
            </div>
        </div>
    </div>
</header>

<main class="site-main">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="site-footer-grid">
            <div class="site-footer-brand">
                <div class="footer-logo">{{ $siteName }}</div>
                @if($tagline)<p>{{ $tagline }}</p>@endif
            </div>
            <div class="site-footer-col">
                <h4>Navigation</h4>
                <ul>
                    @foreach($navItems->take(6) as $navItem)
                        @php $isIntranet = $navItem->blueprint?->identifier === 'intranet_page'; @endphp
                        @if(!$isIntranet || $isPortalAuth)
                            <li><a href="{{ \Marble\Admin\Facades\Marble::url($navItem) }}">{{ $navItem->name() }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="site-footer-col">
                <h4>Connect</h4>
                <ul>
                    @if($instagram)<li><a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a></li>@endif
                    @if($facebook)<li><a href="{{ $facebook }}"  target="_blank" rel="noopener">Facebook</a></li>@endif
                    @if($linkedin)<li><a href="{{ $linkedin }}"  target="_blank" rel="noopener">LinkedIn</a></li>@endif
                    <li><a href="{{ route('marble.portal.login') }}">Member Login</a></li>
                </ul>
            </div>
        </div>
        <div class="site-footer-bottom">
            <div>{{ $copyright ?: '&copy; ' . date('Y') . ' ' . $siteName }} &mdash; Powered by <a href="https://github.com/marble-cms/marble" style="color:#8899aa">Marble CMS</a></div>
            <div class="site-footer-social">
                @if($instagram)<a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a>@endif
                @if($facebook)<a href="{{ $facebook }}" target="_blank" rel="noopener">Facebook</a>@endif
                @if($linkedin)<a href="{{ $linkedin }}" target="_blank" rel="noopener">LinkedIn</a>@endif
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
