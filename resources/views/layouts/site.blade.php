<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site->site_name) | {{ $site->site_name }}</title>

    @if ($site->faviconUrl())
        <link rel="icon" href="{{ $site->faviconUrl() }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <script>document.documentElement.classList.add('js');</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            var toggle = document.getElementById('nav-toggle');
            var links = document.getElementById('nav-links');

            function onScroll() { nav.classList.toggle('is-scrolled', window.scrollY > 8); }
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });

            toggle.addEventListener('click', function () {
                var open = links.classList.toggle('is-open');
                toggle.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

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
                el.style.transitionDelay = (i % 4) * 70 + 'ms';
                io.observe(el);
            });
        })();
    </script>
</body>
</html>