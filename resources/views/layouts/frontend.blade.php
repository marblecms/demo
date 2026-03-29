<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
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
        }
        .site-logo:hover { text-decoration: none; color: #fff; }

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
    </style>
    @stack('styles')
</head>
<body>

<header class="site-header">
    <div class="site-header-inner">
        <a href="/" class="site-logo">{{ config('app.name', 'Marble') }}</a>
        <nav class="site-nav">
            @foreach(\Marble\Admin\Facades\Marble::nav(null, 1) as $navItem)
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
    &copy; {{ date('Y') }} {{ config('app.name', 'Marble') }} &mdash; Powered by <a href="https://github.com/marble-cms/marble">Marble CMS</a>
</footer>

@stack('scripts')
</body>
</html>
