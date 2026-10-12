<header class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ $site->site_name }}">
            @if ($site->logoUrl())
                <img src="{{ $site->logoUrl() }}" alt="{{ $site->site_name }}">
            @else
                <span class="brand-text">{{ strtoupper($site->site_name) }}</span>
            @endif
        </a>

        <nav class="nav-links" id="nav-links">
            @foreach (config('navigation') as $item)
                <a href="{{ route($item['route']) }}"
                   class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">{{ __($item['label']) }}</a>
            @endforeach
        </nav>

        <div class="nav-actions">
            <div class="lang-switch">
                @foreach (['id' => 'ID', 'en' => 'EN'] as $code => $label)
                    <a href="{{ route(request()->route()->getName(), ['locale' => $code]) }}"
                       class="{{ app()->getLocale() === $code ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            @if ($site->email)
                <a href="mailto:{{ $site->email }}" class="btn btn-primary btn-sm nav-cta">{{ __('Hubungi Kami') }}</a>
            @endif

            <button type="button" class="nav-toggle" id="nav-toggle" aria-label="Menu" aria-expanded="false" aria-controls="nav-links">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>