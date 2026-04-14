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

    <link rel="stylesheet" href="/css/frontend.css">
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
        </div>
    </div>
</header>

<main class="site-main {{ $__env->hasSection('hero') ? 'has-hero' : '' }}">@yield('content')</main>

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
<script>
(function () {
    var header = document.querySelector('.site-header');
    var hero   = document.getElementById('hero');
    if (!header || !hero) return;

    function update() {
        var heroBottom = hero.getBoundingClientRect().bottom;
        if (heroBottom > 0) {
            header.classList.add('is-hero');
        } else {
            header.classList.remove('is-hero');
        }
    }

    update();
    window.addEventListener('scroll', update, { passive: true });
})();
</script>
</body>
</html>
