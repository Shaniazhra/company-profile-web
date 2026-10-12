<footer class="footer">
    <div class="container footer-inner">
        <div>
            <a href="{{ route('home') }}" class="brand">
                @if ($site->logoUrl())
                    <img src="{{ $site->logoUrl() }}" alt="{{ $site->site_name }}">
                @else
                    <span class="brand-text">{{ strtoupper($site->site_name) }}</span>
                @endif
            </a>
            @if ($site->tagline)
                <p>{{ $site->tagline }}</p>
            @endif
        </div>

        <div class="footer-links">
            <h4>{{ __('Navigasi') }}</h4>
            @foreach (config('navigation') as $item)
                <a href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>
            @endforeach
        </div>

        <div class="footer-links">
            <h4>{{ __('Kontak') }}</h4>
            @if ($site->email)
                <a href="mailto:{{ $site->email }}">{{ $site->email }}</a>
            @endif
            @if ($site->instagram_url)
                <a href="{{ $site->instagram_url }}" target="_blank" rel="noopener">Instagram</a>
            @endif
            @if ($site->linkedin_url)
                <a href="{{ $site->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
            @endif
        </div>
    </div>

    <div class="container footer-bottom">
        <p>&copy; {{ date('Y') }} {{ $site->site_name }}. {{ __('Seluruh hak cipta dilindungi.') }}</p>
    </div>
</footer>