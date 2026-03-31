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
            background: #f5f7fa;
            color: #333;
            font-size: 15px;
            line-height: 1.7;
        }
        a { color: #2258A8; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Header */
        .site-header {
            background: linear-gradient(to bottom, #2258A8 0%, #163C80 100%);
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.25);
        }
        .site-header-inner {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 24px;
            height: 56px;
        }
        .site-logo {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .3px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .site-logo:hover { text-decoration: none; color: #fff; }
        .site-logo img { height: 28px; width: auto; display: block; }

        /* Nav */
        .site-nav {
            display: flex;
            gap: 4px;
            flex: 1;
        }
        .site-nav a {
            color: rgba(255,255,255,.85);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            transition: background .15s, color .15s;
        }
        .site-nav a:hover { background: rgba(255,255,255,.15); color: #fff; text-decoration: none; }
        .site-nav a.active { background: rgba(255,255,255,.2); color: #fff; font-weight: 600; }

        /* Main */
        .site-main {
            max-width: 960px;
            margin: 40px auto;
            padding: 0 24px;
        }

        /* Content card */
        .content-card {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            padding: 36px 40px;
        }
        .content-card h1 {
            margin: 0 0 24px;
            font-size: 26px;
            font-weight: 700;
            color: #1a1a2e;
            border-bottom: 2px solid #e8edf3;
            padding-bottom: 16px;
        }

        /* Footer */
        .site-footer {
            text-align: center;
            color: #aaa;
            font-size: 12px;
            padding: 32px 24px;
            margin-top: 48px;
            border-top: 1px solid #e0e6ee;
        }
        .site-footer a { color: #aaa; }
        .site-footer a:hover { color: #888; }
        .site-footer-social { display: flex; justify-content: center; gap: 16px; margin-bottom: 10px; }
        .site-footer-social a { color: #bbb; font-size: 13px; }
        .site-footer-social a:hover { color: #888; text-decoration: none; }
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
                {{ $siteName }}
            @endif
        </a>
        <nav class="site-nav">
            @foreach(\Marble\Admin\Facades\Marble::navigation(null, 1) as $navItem)
                @php $navUrl = \Marble\Admin\Facades\Marble::url($navItem); @endphp
                <a href="{{ $navUrl }}"
                   class="{{ ('/' . request()->path()) === $navUrl ? 'active' : '' }}">
                    {{ $navItem->name() }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

<main class="site-main">
    @yield('content')
</main>

<footer class="site-footer">
    @if($instagram || $facebook || $linkedin)
        <div class="site-footer-social">
            @if($instagram) <a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a> @endif
            @if($facebook)  <a href="{{ $facebook }}"  target="_blank" rel="noopener">Facebook</a>  @endif
            @if($linkedin)  <a href="{{ $linkedin }}"  target="_blank" rel="noopener">LinkedIn</a>  @endif
        </div>
    @endif
    <div>
        {{ $copyright ?: '&copy; ' . date('Y') . ' ' . $siteName }}
        &mdash; Powered by <a href="https://github.com/marble-cms/marble">Marble CMS</a>
    </div>
</footer>

@stack('scripts')
</body>
</html>
