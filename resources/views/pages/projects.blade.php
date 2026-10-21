@extends('layouts.site')

@section('title', __('Projects'))

{{-- Projects-only CSS. Colors, fonts, text sizes, and radius come from the tokens in theme.css. --}}
@push('head')
@php
    // Site icon: use the favicon column if set, otherwise the logo.
    $siteRow     = $site ?? \App\Models\SiteSetting::first();
    $heroUrl     = $siteRow?->projects_image ? asset('storage/'.$siteRow->projects_image) : null;
    // Position and zoom set in the admin (Projects list). Plain numbers, so the decimal separator is always a dot.
    $num         = fn ($v, $d) => rtrim(rtrim(number_format((float) ($v ?? $d), 2, '.', ''), '0'), '.');
    $heroStyle   = '--fx:'.$num($siteRow?->projects_image_x, 50).'%;--fy:'.$num($siteRow?->projects_image_y, 50).'%;--z:'.$num($siteRow?->projects_image_zoom, 1);
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
    .pj {
        --ink: #1a1433;
        --ink-soft: #5d5873;
        --violet-deep: #5b1f8e;
        --line: rgba(26, 20, 51, 0.14);
        overflow-x: clip;
    }
    .pj a:focus-visible { outline: 3px solid var(--violet-deep); outline-offset: 3px; }

    /* Hero: a short dark banner with the breadcrumb and the page title */
    .pj-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin-top: calc(var(--nav-height) * -1);
        padding: calc(var(--nav-height) + clamp(2.5rem, 6vw, 4rem)) 0 clamp(2.5rem, 6vw, 4rem);
        text-align: center;
        background: linear-gradient(180deg, #1a1140 0%, #120b2b 100%);
    }
    .pj-hero-bg { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; object-position: var(--fx, 50%) var(--fy, 50%); transform: scale(var(--z, 1)); transform-origin: var(--fx, 50%) var(--fy, 50%); }
    .pj-hero.has-image::before { content: ""; position: absolute; inset: 0; z-index: -1; background: rgba(18, 11, 43, 0.8); }
    .pj-hero .container { position: relative; z-index: 1; }
    .pj-crumbs { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.5rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.85); }
    .pj-crumbs a { color: #fff; text-decoration: none; }
    .pj-crumbs a:hover { text-decoration: underline; }
    .pj-crumbs [aria-current] { font-weight: 600; color: #fff; }
    .pj-hero h1 { margin: 0.6rem 0 0; font-size: var(--fs-h1); font-weight: 700; line-height: 1.1; letter-spacing: -0.02em; color: #fff; text-wrap: balance; }
    .pj-lead { max-width: 40ch; margin: 0.9rem auto 0; font-size: var(--fs-body); line-height: 1.6; color: rgba(255, 255, 255, 0.85); }
    .pj .pj-hero a:focus-visible { outline-color: #fff; }

    /* List */
    .pj-sec { padding-block: clamp(3rem, 7vw, 5.5rem); background: #fff; color: var(--ink); }
    .pj-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 19rem), 1fr)); gap: clamp(1.25rem, 3vw, 2rem); max-width: 64rem; margin-inline: auto; }
    .pj-empty { max-width: 32rem; margin: 0 auto; padding: 3rem 1rem; text-align: center; font-size: var(--fs-small); color: var(--ink-soft); }

    .pj-card { display: block; color: inherit; text-decoration: none; }
    .pj-media {
        position: relative;
        isolation: isolate;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #26262c;
        border-radius: var(--radius-lg);
        box-shadow: 0 18px 32px -26px rgba(36, 26, 74, 0.6);
    }
    .pj-media img.pj-photo { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .pj-card:hover .pj-photo { transform: scale(1.04); }
    .pj-body { padding: 0.9rem 0.25rem 0; }
    .pj-name { margin: 0; font-family: var(--font-heading); font-size: var(--fs-h4); font-weight: 700; line-height: 1.25; color: var(--ink); }
    .pj-meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.35rem 1rem; margin-top: 0.35rem; font-size: var(--fs-small); }
    .pj-tag { color: var(--ink-soft); }
    .pj-more { font-weight: 600; color: var(--color-primary); }
    .pj-more span { display: inline-block; transition: transform 0.2s ease; }
    .pj-card:hover .pj-more span { transform: translateX(0.25rem); }

    @media (prefers-reduced-motion: reduce) {
        .pj-media img.pj-photo { transition: none; }
        .pj-card:hover .pj-photo { transform: none; }
    }
</style>
@endpush

@section('content')
@php
    $loc     = app()->getLocale();
    $homeUrl = Route::has('home') ? route('home', ['locale' => $loc]) : url('/');
    $heroUrl = $heroUrl ?? null;
@endphp

<div class="pj">

    {{-- Hero: a short banner with the breadcrumb and the page title --}}
    <section class="pj-hero {{ $heroUrl ? 'has-image' : '' }}">
        @if ($heroUrl)<img class="pj-hero-bg" src="{{ $heroUrl }}" alt="" style="{{ $heroStyle ?? '' }}">@endif
        <div class="container" data-reveal>
            <nav class="pj-crumbs" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ $homeUrl }}">{{ __('Home') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span aria-current="page">{{ __('Projects') }}</span>
            </nav>
            <h1>{{ __('Projects') }}</h1>
            <p class="pj-lead">{{ __('Series and films made with our real-time pipeline.') }}</p>
        </div>
    </section>

    {{-- Project list --}}
    <section class="pj-sec" aria-label="{{ __('Projects') }}">
        <div class="container">
            @if ($projects->isEmpty())
                <p class="pj-empty">{{ __('Projects will be listed here soon.') }}</p>
            @else
                <div class="pj-grid">
                    @foreach ($projects as $p)
                        @php
                            $name = $p->title ?: $p->name;
                            $meta = collect([$p->tag, $p->release_year])->filter()->implode('  /  ');
                        @endphp
                        <a class="pj-card" href="{{ route('projects.show', ['locale' => $loc, 'project' => $p->slug]) }}" data-reveal>
                            <div class="pj-media">
                                @if ($p->image_url)<img class="pj-photo" src="{{ $p->image_url }}" alt="" loading="lazy">@endif
                            </div>
                            <div class="pj-body">
                                <h2 class="pj-name">{{ $name }}</h2>
                                <div class="pj-meta">
                                    <span class="pj-tag">{{ $meta }}</span>
                                    <span class="pj-more">{{ __('View project') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection