@extends('layouts.site')

@section('title', __('Our team'))

{{-- Team-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css. --}}
@push('head')
@php
    // Site icon: use the favicon column if set, otherwise the logo.
    $siteRow     = $site ?? \App\Models\SiteSetting::first();
    $heroUrl     = $siteRow?->team_image ? asset('storage/'.$siteRow->team_image) : null;
    // Position and zoom set in the admin (Team list). Plain numbers, so the decimal separator is always a dot.
    $num         = fn ($v, $d) => rtrim(rtrim(number_format((float) ($v ?? $d), 2, '.', ''), '0'), '.');
    $heroStyle   = '--fx:'.$num($siteRow?->team_image_x, 50).'%;--fy:'.$num($siteRow?->team_image_y, 50).'%;--z:'.$num($siteRow?->team_image_zoom, 1);
    $faviconPath = $siteRow?->favicon ?: $siteRow?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($siteRow?->updated_at?->timestamp ?? 1)
        : null;
@endphp

@vite(['resources/css/theme.css'])

@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
@endif

<style>
    .tm {
        --paper: color-mix(in srgb, var(--color-primary) 9%, #fff);
        --ink: #1a1433;
        --ink-soft: #5d5873;
        --violet-deep: #5b1f8e;
        --line: rgba(26, 20, 51, 0.14);
        overflow-x: clip;
    }
    .tm h1, .tm h2, .tm h3 { text-wrap: balance; }
    .tm a:focus-visible { outline: 3px solid var(--violet-deep); outline-offset: 3px; }

    /* Hero: a short dark banner with the breadcrumb and the page title */
    .tm-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin-top: calc(var(--nav-height) * -1);
        padding: calc(var(--nav-height) + clamp(2.5rem, 6vw, 4rem)) 0 clamp(2.5rem, 6vw, 4rem);
        text-align: center;
        background: linear-gradient(180deg, #1a1140 0%, #120b2b 100%);
    }
    .tm-hero-bg { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; object-position: var(--fx, 50%) var(--fy, 50%); transform: scale(var(--z, 1)); transform-origin: var(--fx, 50%) var(--fy, 50%); }
    .tm-hero.has-image::before { content: ""; position: absolute; inset: 0; z-index: -1; background: rgba(18, 11, 43, 0.8); }
    .tm-hero .container { position: relative; z-index: 1; }
    .tm-crumbs { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.5rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.85); }
    .tm-crumbs a { color: #fff; text-decoration: none; }
    .tm-crumbs a:hover { text-decoration: underline; }
    .tm-crumbs [aria-current] { font-weight: 600; color: #fff; }
    .tm-hero h1 { margin: 0.6rem 0 0; font-size: var(--fs-h1); font-weight: 700; line-height: 1.1; letter-spacing: -0.02em; color: #fff; }
    .tm-lead { max-width: 40ch; margin: 0.9rem auto 0; font-size: var(--fs-body); line-height: 1.6; color: rgba(255, 255, 255, 0.85); }
    .tm .tm-hero a:focus-visible { outline-color: #fff; }

    /* Team list */
    .tm-sec { padding-block: clamp(3rem, 7vw, 5.5rem); background: #fff; color: var(--ink); }
    .tm-empty { max-width: 32rem; margin: 0 auto; padding: 3rem 1rem; text-align: center; font-size: var(--fs-small); color: var(--ink-soft); }

    /* Card: same as Home and About. A tall purple tab on the left with the brand name running up it,
       the person in front, and a gradient behind the name and role. Transparent PNG cutouts work best. */
    .crew { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 13rem), 1fr)); gap: clamp(1.5rem, 3vw, 2.25rem) 1.5rem; max-width: 62rem; margin-inline: auto; }
    .crew-item { width: 100%; max-width: 16rem; margin-inline: auto; }
    .crew-card {
        /* Tab width as a share of the card. Bigger = wider purple tab. */
        --tab: 48%;
        position: relative;
        isolation: isolate;
        aspect-ratio: 3 / 4;
        overflow: hidden;
        border-radius: 1.5rem 1.5rem 0.5rem 0.5rem;
        color: #fff;
        box-shadow: 0 22px 36px -28px rgba(36, 26, 74, 0.7);
    }
    .crew-card::before {
        content: "";
        position: absolute;
        z-index: 0;
        top: 0;
        bottom: 0;
        left: 0;
        width: var(--tab);
        border-radius: 1.5rem 1.5rem 0 0;
        background: linear-gradient(170deg, var(--color-primary) 0%, var(--violet-deep) 100%);
    }
    .crew-mark {
        position: absolute;
        z-index: 0;
        top: 0.9rem;
        left: calc(var(--tab) * 0.3);
        transform: translateX(-50%) rotate(180deg);
        writing-mode: vertical-rl;
        font-family: var(--font-heading);
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 700;
        line-height: 1;
        color: rgba(255, 255, 255, 0.2);
        white-space: nowrap;
        pointer-events: none;
    }
    .crew-photo {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: grid;
        place-items: center;
        font-family: var(--font-heading);
        font-size: clamp(3rem, 8vw, 4.5rem);
        font-weight: 600;
        color: var(--violet-deep);
    }
    .crew-photo img {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center bottom;
        transform-origin: center bottom;
        transition: transform 0.5s ease;
    }
    .crew-card:hover .crew-photo img { transform: scale(1.03); }
    .crew-card::after {
        content: "";
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 2;
        height: 40%;
        background: linear-gradient(to top, rgba(139, 47, 208, 1) 0%, rgba(139, 47, 208, 0.9) 30%, rgba(139, 47, 208, 0) 100%);
        pointer-events: none;
    }
    .crew-info { position: absolute; right: 1rem; bottom: 1rem; left: 1rem; z-index: 3; }
    .crew-card h2 { margin: 0; font-size: var(--fs-h4); font-weight: 700; line-height: 1.2; color: #fff; }
    .crew-role { margin: 0.2rem 0 0; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.9); }

    /* Credits under the card */
    .crew-credits { display: grid; gap: 0.4rem; margin: 1rem 0.25rem 0; padding: 0; list-style: none; }
    .crew-credits li { position: relative; padding-left: 1.1rem; font-size: var(--fs-small); line-height: 1.45; color: var(--ink-soft); }
    .crew-credits li::before { content: ""; position: absolute; top: 0.5em; left: 0; width: 0.4rem; height: 0.4rem; border-radius: 50%; background: var(--color-primary); }

    @media (prefers-reduced-motion: reduce) {
        .crew-photo img { transition: none; }
        .crew-card:hover .crew-photo img { transform: none; }
    }
</style>
@endpush

@section('content')
@php
    $loc       = app()->getLocale();
    $homeUrl   = Route::has('home') ? route('home', ['locale' => $loc]) : url('/');
    $heroUrl   = $heroUrl ?? null;
    $brandName = $brand ?? (($site ?? \App\Models\SiteSetting::first())?->site_name ?: 'Dream Ratio');
@endphp

<div class="tm">

    {{-- Hero: a short banner with the breadcrumb and the page title --}}
    <section class="tm-hero {{ $heroUrl ? 'has-image' : '' }}">
        @if ($heroUrl)<img class="tm-hero-bg" src="{{ $heroUrl }}" alt="" style="{{ $heroStyle ?? '' }}">@endif
        <div class="container" data-reveal>
            <nav class="tm-crumbs" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ $homeUrl }}">{{ __('Home') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span aria-current="page">{{ __('Our team') }}</span>
            </nav>
            <h1>{{ __('The team behind') }} {{ $brandName }}</h1>
            <p class="tm-lead">{{ __('An R&D team with decades of experience in the animation industry.') }}</p>
        </div>
    </section>

    {{-- Team --}}
    <section class="tm-sec" aria-label="{{ __('Our team') }}">
        <div class="container">
            @if ($members->isEmpty())
                <p class="tm-empty">{{ __('Our team will be listed here soon.') }}</p>
            @else
                <div class="crew">
                    @foreach ($members as $m)
                        <article class="crew-item" data-reveal>
                            <div class="crew-card">
                                <span class="crew-mark" aria-hidden="true">{{ $brandName }}</span>
                                <div class="crew-photo">
                                    @if ($m->photo_url)
                                        <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" loading="lazy">
                                    @else
                                        <span aria-hidden="true">{{ collect(explode(' ', $m->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}</span>
                                    @endif
                                </div>
                                <div class="crew-info">
                                    <h2>{{ $m->name }}</h2>
                                    <p class="crew-role">{{ $m->role }}</p>
                                </div>
                            </div>

                            @if ($m->creditList())
                                <ul class="crew-credits">
                                    @foreach ($m->creditList() as $credit)<li>{{ $credit }}</li>@endforeach
                                </ul>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection