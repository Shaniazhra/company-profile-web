@php
    $loc   = app()->getLocale();
    $home  = Route::has('home') ? route('home', ['locale' => $loc]) : url('/');
    $brand = ($site->site_name ?? null) ?: 'Dream Ratio';

    $navItems = [
        ['label' => __('Home'), 'href' => $home, 'active' => request()->routeIs('home')],
    ];
    if (Route::has('about')) {
        $navItems[] = ['label' => __('About'), 'href' => route('about', ['locale' => $loc]), 'active' => request()->routeIs('about')];
    }
    if (Route::has('pipeline')) {
        $navItems[] = ['label' => __('Production Pipeline'), 'href' => route('pipeline', ['locale' => $loc]), 'active' => request()->routeIs('pipeline')];
    }
    // Team opens the Team page (team). Falls back to the Home section if that route does not exist.
    $navItems[] = Route::has('team')
        ? ['label' => __('Team'), 'href' => route('team', ['locale' => $loc]), 'active' => request()->routeIs('team')]
        : ['label' => __('Team'), 'href' => $home.'#team', 'active' => false];
    // Projects opens the Projects page (projects.index). Falls back to the Home section if that route does not exist.
    $navItems[] = Route::has('projects.index')
        ? ['label' => __('Projects'), 'href' => route('projects.index', ['locale' => $loc]), 'active' => request()->routeIs('projects.*')]
        : ['label' => __('Projects'), 'href' => $home.'#projects', 'active' => false];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $loc) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Home')) | {{ $brand }}</title>
    <meta name="description" content="@yield('description', 'Dream Ratio: We Animate Dreams')">

    {{-- A page can put its biggest photo here (@push('preload')) so the browser starts loading it first. --}}
    @stack('preload')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- The font file loads without blocking the first paint. The text shows in a fallback font until it arrives (display=swap). --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap"></noscript>

    @vite(['resources/css/theme.css', 'resources/js/site.js'])
    <script>document.documentElement.classList.add('js');</script>
    @stack('head')
</head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        (function () {
            var nav = document.getElementById('navbar');
            var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 12); };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });

            var toggle = document.getElementById('nav-toggle');
            var links = document.getElementById('nav-links');
            var close = function () {
                links.classList.remove('is-open');
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            };
            toggle.addEventListener('click', function () {
                var open = links.classList.toggle('is-open');
                toggle.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            links.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', close); });

            var items = document.querySelectorAll('[data-reveal]');
            if (!('IntersectionObserver' in window)) {
                items.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12 });
            items.forEach(function (el, i) {
                el.style.transitionDelay = (i % 4) * 80 + 'ms';
                io.observe(el);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>