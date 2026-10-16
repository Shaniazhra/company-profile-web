@php
    // To be replaced with data from the admin panel (stage 5)
    $contacts = [
        ['name' => 'Fairra Tri Agustint', 'phone' => '+62 895 1060 5990'],
        ['name' => 'Rikky Risdiansah',    'phone' => '+62 812 968 50555'],
    ];

    $phoneIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
    $instagramIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><path d="M17.5 6.5h.01"/></svg>';
    $linkedinIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>';

    $instagramUrl = 'https://instagram.com/dream_ratio';
    $linkedinUrl  = 'https://www.linkedin.com/search/results/all/?keywords=Dream%20Ratio';

    // Photo for the contact band. Best: a cut-out PNG with a transparent background at
    // public/images/footer/contact.png (or .webp/.jpg). Otherwise the hero image is used.
    $ctaCutout = null;
    foreach (['png', 'webp', 'jpg', 'jpeg'] as $ext) {
        $path = "images/footer/contact.{$ext}";
        if (file_exists(public_path($path))) {
            $ctaCutout = asset($path);
            break;
        }
    }
    $ctaImg = $ctaCutout ?: (!empty($site->hero_image) ? asset('storage/'.$site->hero_image) : null);
@endphp

@vite(['resources/css/theme.css'])

@once
<style>
    /* Footer: one dark block. A purple contact band on top, link columns below. */
    .sf { --focus: #d9b8f7; color: var(--color-text); background: var(--color-bg); }

    .sf-top {
        --focus: #fff;
        --sf-pad: clamp(1.25rem, 3vw, 2rem);
        --sf-vis: clamp(11rem, 20vw, 17rem);
        --sf-gutter: max(1.25rem, calc((100% - var(--container)) / 2));
        --sf-fade: 90deg;
        position: relative;
        overflow: hidden;
        padding-block: var(--sf-pad);
        color: #fff;
        background: linear-gradient(120deg, #2a1670 0%, #4a1f8f 60%, #5b2aa8 100%);
        scroll-margin-top: calc(var(--nav-height) + 1rem);
    }
    .sf-top-inner { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 1rem clamp(1.25rem, 3vw, 2.5rem); align-items: center; }
    .sf-top-inner.has-visual { grid-template-columns: var(--sf-vis) minmax(0, 1.25fr) minmax(0, 1fr); }
    .sf-top-inner.has-visual::before { content: ""; }

    /* The photo is pinned to the left edge of the band and fades out toward the right, so it blends into the purple. */
    .sf-visual {
        position: absolute;
        inset-block: 0;
        left: 0;
        width: calc(var(--sf-gutter) + var(--sf-vis));
    }
    .sf-visual img { width: 100%; height: 100%; object-fit: cover; object-position: center top; }
    .sf-visual:not(.sf-visual--cutout) {
        -webkit-mask-image: linear-gradient(var(--sf-fade), #000 40%, transparent 100%);
        mask-image: linear-gradient(var(--sf-fade), #000 40%, transparent 100%);
    }
    .sf-visual--cutout img { object-fit: contain; object-position: right bottom; }

    .sf-pitch h2 { margin: 0; max-width: 24ch; font-size: var(--fs-h2); line-height: 1.2; letter-spacing: -0.01em; color: #fff; text-wrap: balance; }
    .sf-pitch p { max-width: 40ch; margin: 0.4rem 0 0; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.92); }

    .sf-contacts { display: grid; gap: 0.5rem; margin: 0; padding: 0; list-style: none; }
    .sf-contacts a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-height: 3rem;
        padding: 0.55rem 1rem;
        color: #fff;
        text-decoration: none;
        background: rgba(10, 7, 26, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--radius);
        transition: background-color 0.2s, border-color 0.2s;
    }
    .sf-contacts a:hover { background: rgba(10, 7, 26, 0.8); border-color: #fff; }
    .sf-contacts svg { flex: none; width: 1.15rem; height: 1.15rem; }
    .sf-contact-text { display: grid; min-width: 0; line-height: 1.3; }
    .sf-contact-text small { font-size: var(--fs-caption); color: rgba(255, 255, 255, 0.85); }
    .sf-contact-text strong { font-size: var(--fs-small); font-weight: 600; }

    .sf-main { padding-block: clamp(1.5rem, 3vw, 2.25rem); }
    .sf-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 1.5rem; }
    .sf-brand p { max-width: 24rem; margin: 0.6rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }
    .sf-social { display: flex; gap: 0.5rem; margin-top: 0.9rem; }
    .sf-social a {
        display: grid;
        place-items: center;
        width: 2.25rem;
        height: 2.25rem;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 50%;
        transition: background-color 0.2s, border-color 0.2s;
    }
    .sf-social a:hover { background: var(--color-primary); border-color: var(--color-primary); }
    .sf-social svg { width: 1rem; height: 1rem; }

    .sf-title { margin: 0 0 0.25rem; font-size: var(--fs-small); font-weight: 600; color: #fff; }
    .sf-links { display: flex; flex-direction: column; }
    .sf-links a {
        display: flex;
        align-items: center;
        min-height: 2.25rem;
        font-size: var(--fs-small);
        color: var(--color-muted);
        text-decoration: none;
        transition: color 0.2s;
    }
    .sf-links a:hover { color: #fff; }

    .sf-bottom { border-top: 1px solid rgba(255, 255, 255, 0.14); }
    .sf-bottom-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0 1.5rem; }
    .sf-bottom p { margin: 0; padding-block: 0.75rem; font-size: var(--fs-small); }
    .sf-bottom a { display: inline-flex; align-items: center; min-height: 2.25rem; font-size: var(--fs-small); color: var(--color-muted); text-decoration: none; }
    .sf-bottom a:hover { color: #fff; }

    @media (max-width: 900px) {
        .sf-top { --sf-fade: 180deg; }
        .sf-top-inner, .sf-top-inner.has-visual { grid-template-columns: 1fr; }
        .sf-top-inner.has-visual::before { display: none; }

        /* On small screens the photo becomes a banner above the text and fades downward. */
        .sf-visual { position: relative; left: auto; width: 100%; height: 9rem; margin: calc(var(--sf-pad) * -1) 0 0.75rem; }
        .sf-visual--cutout img { object-position: center bottom; }

        .sf-grid { grid-template-columns: 1fr 1fr; }
        .sf-brand { grid-column: 1 / -1; }

        /* Touch screens get the full 44px tap size. */
        .sf-links a, .sf-bottom a { min-height: 2.75rem; }
        .sf-social a { width: 2.75rem; height: 2.75rem; }
    }
    @media (max-width: 480px) {
        .sf-grid { grid-template-columns: 1fr; }
        .sf-brand { grid-column: auto; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sf-contacts a, .sf-social a, .sf-links a { transition: none; }
    }
</style>
@endonce

<footer class="sf">
    <section class="sf-top" id="contact" aria-labelledby="sf-cta-title">
        @if ($ctaImg)
            <div class="sf-visual {{ $ctaCutout ? 'sf-visual--cutout' : '' }}" aria-hidden="true">
                <img src="{{ $ctaImg }}" alt="" loading="lazy">
            </div>
        @endif

        <div class="container sf-top-inner {{ $ctaImg ? 'has-visual' : '' }}">
            <div class="sf-pitch">
                <h2 id="sf-cta-title">{{ __('Got an animation project? Tell us what you need.') }}</h2>
                <p>{{ __('Call one of us directly.') }}</p>
            </div>

            <ul class="sf-contacts">
                @foreach ($contacts as $c)
                    <li>
                        <a href="tel:+{{ preg_replace('/\D+/', '', $c['phone']) }}">
                            {!! $phoneIcon !!}
                            <span class="sf-contact-text">
                                <small>{{ $c['name'] }}</small>
                                <strong>{{ $c['phone'] }}</strong>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <div class="sf-main">
        <div class="container sf-grid">
            <div class="sf-brand">
                <a href="{{ $home }}" class="brand" aria-label="{{ $brand }}">
                    <x-brand-logo :site="$site" :brand="$brand" size="sm" />
                </a>
                <p>{{ ($site->tagline ?? null) ?: 'We Animate Dreams' }}</p>

                <div class="sf-social">
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" aria-label="Instagram">{!! $instagramIcon !!}</a>
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" aria-label="LinkedIn">{!! $linkedinIcon !!}</a>
                </div>
            </div>

            <nav aria-label="{{ __('Footer menu') }}">
                <h3 class="sf-title">{{ __('Menu') }}</h3>
                <div class="sf-links">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </nav>

            <div>
                <h3 class="sf-title">{{ __('Contact') }}</h3>
                <div class="sf-links">
                    <a href="{{ $home }}#contact">{{ __('Contact us') }}</a>
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener">Instagram</a>
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>

    <div class="sf-bottom">
        <div class="container sf-bottom-inner">
            <p>© {{ date('Y') }} {{ $brand }}. {{ __('All rights reserved.') }}</p>
            <a href="#">{{ __('Back to top') }}</a>
        </div>
    </div>
</footer>