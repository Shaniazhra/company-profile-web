<header class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="{{ $home }}" class="brand" aria-label="{{ $brand }}">
            <x-brand-logo :site="$site" :brand="$brand" size="md" />
        </a>

        <nav class="nav-links" id="nav-links" aria-label="{{ __('Main navigation') }}">
            @foreach ($navItems as $item)
                <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }}" @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="nav-actions">
            <a href="{{ $home }}#kontak" class="btn btn-primary btn-sm nav-cta">{{ __('Contact us') }}</a>

            <button class="nav-toggle" id="nav-toggle" type="button"
                    aria-label="{{ __('Open menu') }}" aria-expanded="false" aria-controls="nav-links">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

@vite(['resources/css/theme.css'])

@once
<style>
    /* The brand name follows the bar: light on the dark bar, dark on the white bar after scrolling. */
    .navbar .brand { color: var(--color-text); transition: color 0.25s; }
    @media (min-width: 901px) {
        .navbar.is-scrolled .brand { color: #241a4a; }
    }

    /* Remove the line underneath the navigation menu in every state. */
    .navbar .nav-links {
        border-bottom: none !important;
        box-shadow: none !important;
        background-image: none !important;
    }

    .navbar .nav-links a,
    .navbar .nav-links a:hover,
    .navbar .nav-links a:focus,
    .navbar .nav-links a.active {
        text-decoration: none !important;
        border-bottom: none !important;
        box-shadow: none !important;
        background-image: none !important;
    }

    .navbar .nav-links a::before,
    .navbar .nav-links a::after {
        content: none !important;
        display: none !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .navbar .brand { transition: none; }
    }
</style>
@endonce