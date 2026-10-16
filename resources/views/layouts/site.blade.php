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
    $navItems[] = ['label' => __('Team'), 'href' => $home.'#team', 'active' => false];
    $navItems[] = ['label' => __('Projects'), 'href' => $home.'#projects', 'active' => false];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $loc) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Home')) | {{ $brand }}</title>
    <meta name="description" content="@yield('description', 'Dream Ratio: We Animate Dreams')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/theme.css', 'resources/js/app.js'])
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