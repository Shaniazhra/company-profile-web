@extends('layouts.site')

@section('title', __('Home'))
@section('description', \Illuminate\Support\Str::limit(strip_tags(($site->heart_statement ?? null) ?: $brand), 155))

{{-- Home-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css --}}
@push('preload')
@php
    // The hero photo is the biggest thing on the page, so the browser is told to fetch it first.
    $heroPreload = ! empty($site->hero_image) ? asset('storage/'.$site->hero_image) : null;
@endphp
@if ($heroPreload)
    <link rel="preload" as="image" href="{{ $heroPreload }}" fetchpriority="high">
@endif
@endpush

@push('head')
@php
    // Site icon: use the favicon column if set, otherwise the logo.
    $faviconPath = $site?->favicon ?: $site?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($site?->updated_at?->timestamp ?? 1)
        : null;
@endphp

@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
@endif

<style>
    /* Purple, dark surface, and light text come from theme.css. Only the warm paper
       and ink colors for light sections are defined here, because theme.css has no light surface. */
    .home {
        /* A light tint of the theme purple, so it reads clearly against the white sections. */
        --paper: color-mix(in srgb, var(--color-primary) 9%, #fff);
        --ink: #1a1433;
        --ink-soft: #5d5873;
        --violet-deep: #5b1f8e;
        --panel: var(--color-surface);
        --lilac: var(--color-muted);
        /* Resolved here so dark blocks inside light sections keep the light focus ring. */
        --focus-light: var(--focus);
    }
    .home h1, .home h2, .home h3 { text-wrap: balance; }
    .home [id] { scroll-margin-top: calc(var(--nav-height) + 1rem); }

    /* Section tones: light (paper, white) */
    .tone-paper, .tone-white {
        --color-text: var(--ink);
        --color-muted: var(--ink-soft);
        --color-border: rgba(26, 20, 51, 0.16);
        --line: rgba(26, 20, 51, 0.16);
        --focus: var(--violet-deep);
    }
    .tone-paper { --bg: var(--paper); }
    .tone-white { --bg: #fff; }

    .sec { padding-block: clamp(3rem, 7vw, 5.5rem); background: var(--bg); color: var(--color-text); }
    .sec--tight { padding-block: clamp(2.5rem, 5vw, 3.5rem); }
    .sec--cards { padding-block: 0; }
    .sec h2, .sec h3, .sec h4 { color: var(--color-text); }
    .sec h2 { margin: 0; font-size: var(--fs-h1); line-height: 1.12; letter-spacing: -0.02em; }
    :where(.sec) p { margin: 0; }

    .accent { color: var(--color-primary); }
    .tone-paper .eyebrow, .tone-white .eyebrow { color: var(--color-primary); }

    /* Section heading: centered, with a short description below */
    .sec-head { max-width: 44rem; margin: 0 auto clamp(2rem, 5vw, 3.5rem); text-align: center; }
    .sec-head h2 { margin-top: 0.75rem; }
    .sec-head p { margin: 1rem auto 0; max-width: 40ch; }

    .home a:focus-visible,
    .home .btn:focus-visible,
    .faq-item summary:focus-visible { outline: 3px solid var(--focus, var(--violet-deep)); outline-offset: 3px; }

    /* Image frame (shows a text placeholder until the image exists) */
    .frame-media {
        position: relative;
        display: grid;
        place-items: center;
        aspect-ratio: 16 / 9;
        padding: 1rem;
        overflow: hidden;
        text-align: center;
        background: var(--panel);
        border-radius: var(--radius);
        font-family: var(--font-heading);
        font-size: var(--fs-h4);
        font-weight: 600;
        color: #fff;
    }
    .frame-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .frame-media small { display: block; margin-top: 0.35rem; font-family: var(--font-body); font-size: var(--fs-caption); font-weight: 400; color: var(--lilac); }

    .home-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin-top: calc(var(--nav-height) * -1);
        /* Full-size hero: tall on every screen. Change the middle value (85vh) to make it taller or shorter. */
        min-height: clamp(34rem, 85vh, 52rem);
        padding: calc(var(--nav-height) + clamp(4rem, 18vh, 11rem)) 0 clamp(5rem, 9vw, 7rem);
        background: linear-gradient(180deg, #1a1140 0%, #120b2b 100%);
    }
    .home-hero-bg { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; }
    .home-hero.has-image::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0.55) 55%, rgba(0, 0, 0, 0.25) 100%);
    }
    .home-hero .container { position: relative; z-index: 1; }
    .home-title {
        margin: 0 0 1.25rem;
        max-width: 18ch;
        font-size: var(--fs-display);
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.025em;
        color: #fff;
    }
    .home-hero .lead { margin: 0; color: rgba(255, 255, 255, 0.82); }

    /* Service titles sit in one white bar that straddles the bottom edge of the hero. Titles only. */
    .svc-wrap { position: relative; z-index: 3; margin-top: -2.75rem; }
    .svc-bar {
        padding: 0.75rem;
        background: #fff;
        border-radius: var(--radius-lg);
        /* The ring and deeper shadow keep the bar readable over both the hero and the paper below it. */
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.5), 0 24px 44px -22px rgba(15, 11, 36, 0.6);
    }
    .svc { display: grid; grid-template-columns: repeat(var(--cols, 3), minmax(0, 1fr)); }
    .svc-card {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 3.5rem;
        padding: 0.9rem 1.25rem;
        text-align: center;
    }
    /* A straight line separates the titles */
    .svc-card + .svc-card { border-left: 1px solid var(--line); }
    .svc-card h3 { margin: 0; font-size: var(--fs-h4); font-weight: 600; line-height: 1.3; }

    /* Our services: photo and text, no card box. Spans the full container, so it lines up with the navbar logo and the Contact us button. */
    .svc-photo-grid { display: grid; grid-template-columns: repeat(var(--cols, 3), minmax(0, 1fr)); gap: 2rem 1.5rem; }
    .svc-photo .frame-media { margin-bottom: 1.1rem; aspect-ratio: 16 / 10; border-radius: var(--radius-lg); }
    .svc-photo h3 { margin: 0 0 0.4rem; font-size: var(--fs-h3); line-height: 1.25; }
    .svc-photo p { font-size: var(--fs-small); }

    /* Latest project: full container width, so the image lines up with the navbar logo on the left
       and the text runs to the Contact us button on the right. Image left, details right, vertically centered. */
    .pj { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); gap: clamp(2rem, 5vw, 4rem); align-items: center; }
    .pj-media { display: flex; }
    .pj .frame-media { flex: 1; aspect-ratio: 16 / 10; min-height: 0; border-radius: calc(var(--radius-lg) * 1.5); }
    .pj .frame-media img { object-position: top; }
    .pj-text { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; }
    .pj-text .pj-title { margin: 0 0 0.75rem; font-size: var(--fs-h1); line-height: 1.12; letter-spacing: -0.02em; }
    /* Space between the title logo and the tag. Change the last value (0.75rem) to make it tighter or looser. */
    .pj-logo { margin: 0 0 0.75rem; line-height: 0; }
    .pj-logo img { display: block; width: auto; max-width: min(100%, 16rem); max-height: 4rem; object-fit: contain; }
    .pj-tag { margin: 0; font-size: var(--fs-small); font-weight: 600; line-height: 1.3; color: var(--color-muted); }
    /* pre-line keeps the line breaks typed into the synopsis field in the admin panel. */
    .pj-synopsis { margin: 0.5rem 0 0; line-height: 1.55; color: var(--color-muted); white-space: pre-line; }
    /* Buttons under the synopsis: View details and View projects. */
    .pj-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; }
    .pj-actions .btn { min-height: 2.75rem; }

    /* Team: no background box. Two purple parts like the reference: a tall tab on the left (rounded top,
       about two thirds of the card width) with the brand name running up it, and a gradient at the bottom
       behind the name and role. The person stands IN FRONT of the tab, anchored to the bottom, and is
       wider than the tab, so the shoulders stick out past its right edge. Use transparent PNG cutouts. */
    .crew { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr)); gap: 1.5rem; max-width: 62rem; margin-inline: auto; }
    .crew-item { width: 100%; max-width: 16rem; margin-inline: auto; }
    .crew-card {
        /* Tab width as a share of the card. Bigger = wider purple tab. */
        --tab: 48%;
        position: relative;
        isolation: isolate;
        /* Card shape. 3 / 4 matches the photos (800 x 1067). */
        aspect-ratio: 3 / 4;
        overflow: hidden;
        border-radius: 1.5rem 1.5rem 0.5rem 0.5rem;
        color: #fff;
    }
    /* The tall tab on the left, rounded at the top */
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
    /* Brand name running up the tab, behind the person */
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
        /* Position and zoom come from the admin (--fx, --fy, --z). The defaults match the old look. */
        object-position: var(--fx, 50%) var(--fy, 100%);
        transform: scale(var(--z, 1));
        transform-origin: var(--fx, 50%) var(--fy, 100%);
        transition: transform 0.5s ease;
    }
    .crew-card:hover .crew-photo img { transform: scale(calc(var(--z, 1) * 1.03)); }
    /* Gradient at the bottom, full width, in front of the person, behind the name and role */
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
    .crew-card h3 { margin: 0; font-size: var(--fs-h4); font-weight: 700; line-height: 1.2; color: #fff; }
    .crew-role { margin-top: 0.2rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.9); }
    /* "See more" button under the team cards */
    .crew-more { display: flex; justify-content: center; margin-top: clamp(2rem, 4vw, 3rem); }
    .crew-more .btn { min-height: 2.75rem; }

    /* Clients */
    .clients { text-align: center; }
    .clients .sec-head { margin-bottom: clamp(2.75rem, 6vw, 4rem); }
    .client-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 1.5rem 3.5rem; }
    .client-row span { font-family: var(--font-heading); font-size: var(--fs-h3); font-weight: 600; color: var(--color-text); }
    .client-row img { width: 11rem; height: 3.5rem; object-fit: contain; }

    /* FAQ: accordion on the left, a dark help card on the right. Both columns share the same height.
       The open item turns primary. */
    .faq { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr); gap: clamp(1.5rem, 4vw, 2.5rem); align-items: stretch; max-width: 62rem; margin-inline: auto; }
    .faq-list { display: grid; align-content: start; gap: 0.75rem; }
    .faq-item { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); transition: background-color 0.2s, border-color 0.2s; }
    .faq-item summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 3.25rem;
        padding: 0.9rem 1.25rem;
        font-weight: 500;
        color: var(--color-text);
        cursor: pointer;
        list-style: none;
    }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary::after { content: "+"; content: "+" / ""; flex: none; font-size: var(--fs-h3); line-height: 1; }
    .faq-item[open] { background: var(--color-primary); border-color: var(--color-primary); }
    .faq-item[open] summary { color: #fff; }
    .faq-item[open] summary::after { content: "\2212"; content: "\2212" / ""; }
    .faq-item p { padding: 0 1.25rem 1.25rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.92); }

    .faq-aside {
        --focus: var(--focus-light);
        --color-text: #fff;
        --color-muted: var(--lilac);
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding: 1.25rem;
        max-width: 300px;
        height: fit-content;
        align-self: start;
        background: var(--panel);
        border-radius: var(--radius-lg);
        color: #fff;
    }
    .faq-aside h3 { margin: 0 0 0.6rem; font-size: var(--fs-h3); }
    .faq-aside p { margin-bottom: 1.25rem; font-size: var(--fs-small); }


    @media (max-width: 1000px) {
        .pj, .faq { grid-template-columns: 1fr; }
        .pj { max-width: 34rem; margin-inline: auto; }
        .pj-media { display: block; }
        .pj .frame-media { aspect-ratio: 16 / 10; }
        .pj-text { align-items: center; text-align: center; }
        .pj-actions { justify-content: center; }
        .faq { max-width: 40rem; }
    }
    @media (max-width: 900px) {
        .svc, .svc-photo-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); max-width: 44rem; margin-inline: auto; }
        /* The titles stack in one column, so the lines run across instead of down */
        .svc-bar .svc { grid-template-columns: 1fr; }
        .svc-card + .svc-card { border-left: 0; border-top: 1px solid var(--line); }
    }
    @media (max-width: 560px) {
        .svc, .svc-photo-grid { grid-template-columns: 1fr; max-width: 30rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .svc-card, .faq-item, .crew-photo img { transition: none; }
        .crew-card:hover .crew-photo img { transform: scale(var(--z, 1)); }
    }
</style>
@endpush

@section('content')
@php
    // All content comes from the admin panel through HomeController.
    $heroImg = !empty($site->hero_image) ? asset('storage/'.$site->hero_image) : null;
    $heroPos = max(0, min(100, (int) ($site->hero_focus_x ?? 50))).'% '.max(0, min(100, (int) ($site->hero_focus_y ?? 50))).'%';
    $heart   = $site->heart_statement ?? null;
    $title   = ($site->tagline ?? null) ?: $brand;

    // The home page always shows one project. If the controller sends none, fall back to Ibra so the section never disappears.
    if (empty($featured)) {
        $featured    = ['title' => 'Ibra', 'tag' => 'Season 2', 'synopsis' => null,
                        'logo_url' => file_exists(public_path('images/projects/ibra-logo.png')) ? asset('images/projects/ibra-logo.png') : null];
        $featuredImg = file_exists(public_path('images/projects/ibra.jpg')) ? asset('images/projects/ibra.jpg') : null;
    }
    $featuredImg = $featuredImg ?? null;

    // The title logo stands in for a text title. Accepts a ready URL (logo_url) or a storage path (logo).
    $featuredName = $featured['title'] ?? __('Project');
    $featuredLogo = $featured['logo_url'] ?? null;
    if (! $featuredLogo && ! empty($featured['logo'])) {
        $featuredLogo = preg_match('#^(https?:)?//|^/#', $featured['logo']) ? $featured['logo'] : asset('storage/'.$featured['logo']);
    }

    // Button links for the project section. Each button is hidden when its link is missing.
    $featuredUrl = $featured['url'] ?? null;
    $allProjectsUrl = $projectsUrl ?? null;

    // "Get in Touch" (hero) and "Contact us" (FAQ card) open the Contact page. Falls back to the contact band in the footer.
    $contactUrl = Route::has('contact') ? route('contact', ['locale' => app()->getLocale()]) : '#contact';

    // "See more" button under the team. It opens the team page, and is hidden when that route does not exist.
    $teamUrl = Route::has('team') ? route('team', ['locale' => app()->getLocale()]) : null;

    // Light sections alternate white and tint on their own, whichever optional sections are shown.
    $lightIndex = 0;
    $nextTone   = function () use (&$lightIndex) { return ['tone-white', 'tone-paper'][$lightIndex++ % 2]; };

    // Only services that have a photo appear in the "Our services" section.
    $servicePhotos = collect($services ?? [])->filter(fn ($s) => ! empty($s['img']))->values();
@endphp

<div class="home">

    <section class="home-hero {{ $heroImg ? 'has-image' : '' }}">
        @if ($heroImg)
            <img class="home-hero-bg" src="{{ $heroImg }}" {{ \App\Support\Img::attrs($heroImg) }} alt="" fetchpriority="high" decoding="async" style="object-position: {{ $heroPos }}">
        @endif

        <div class="container">
            <h1 class="home-title">{{ $title }}</h1>
            @if ($heart)<p class="lead">{{ $heart }}</p>@endif
            <div class="btn-row">
                <a href="{{ $contactUrl }}" class="btn btn-primary">{{ __('Get in Touch') }}</a>
            </div>
        </div>
    </section>

    @if (count($services))
    <section class="sec sec--cards tone-white" id="services" aria-label="{{ __('Services') }}">
        <div class="container">
            <div class="svc-wrap">
                <div class="svc-bar">
                    <div class="svc" style="--cols: {{ min(max(count($services), 1), 4) }}">
                        @foreach ($services as $s)
                            <article class="svc-card" data-reveal>
                                <h3>{{ $s['title'] }}</h3>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($servicePhotos->isNotEmpty())
    <section class="sec {{ $nextTone() }}" id="our-services" aria-labelledby="services-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Our services') }}</span>
                <h2 id="services-title">{{ __('What we') }} <span class="accent">{{ __('offer') }}</span></h2>
            </div>

            <div class="svc-photo-grid" style="--cols: {{ min($servicePhotos->count(), 4) }}">
                @foreach ($servicePhotos as $s)
                    <article class="svc-photo" data-reveal>
                        <div class="frame-media"><img src="{{ $s['img'] }}" {{ \App\Support\Img::attrs($s['img']) }} alt="{{ $s['title'] }}" loading="lazy" decoding="async"></div>
                        <h3>{{ $s['title'] }}</h3>
                        <p>{{ $s['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if ($featured)
    <section class="sec {{ $nextTone() }}" id="projects" aria-labelledby="project-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Projects') }}</span>
                <h2 id="project-title">{{ __('Our') }} <span class="accent">{{ __('latest project') }}</span></h2>
            </div>

            <div class="pj">
                <div class="pj-media" data-reveal>
                    <div class="frame-media">
                        @if ($featuredImg)
                            <img src="{{ $featuredImg }}" {{ \App\Support\Img::attrs($featuredImg) }} alt="{{ $featuredName }}" loading="lazy" decoding="async">
                        @else
                            <span>{{ $featuredName }}</span>
                        @endif
                    </div>
                </div>

                <div class="pj-text" data-reveal>
                    @if ($featuredLogo)
                        <h3 class="pj-logo"><img src="{{ $featuredLogo }}" {{ \App\Support\Img::attrs($featuredLogo) }} alt="{{ $featuredName }}" decoding="async"></h3>
                    @else
                        <h3 class="pj-title">{{ $featuredName }}</h3>
                    @endif
                    @if (! empty($featured['tag']))<p class="pj-tag">{{ $featured['tag'] }}</p>@endif
                    @if (! empty($featured['synopsis']))<p class="pj-synopsis">{{ $featured['synopsis'] }}</p>@endif

                    @if ($featuredUrl || $allProjectsUrl)
                        <div class="pj-actions">
                            @if ($featuredUrl)
                                <a href="{{ $featuredUrl }}" class="btn btn-primary">{{ __('View details') }}</a>
                            @endif
                            @if ($allProjectsUrl)
                                <a href="{{ $allProjectsUrl }}" class="btn btn-outline">{{ __('View projects') }}</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    @if (count($team))
    <section class="sec {{ $nextTone() }}" id="team" aria-labelledby="team-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Our team') }}</span>
                <h2 id="team-title">{{ __('The team behind') }} {{ $brand }}</h2>
                <p>{{ __('An R&D team with decades of experience in the animation industry.') }}</p>
            </div>

            <div class="crew">
                @foreach ($team as $p)
                    <article class="crew-item" data-reveal>
                        <div class="crew-card">
                            <span class="crew-mark" aria-hidden="true">{{ $brand }}</span>
                            <div class="crew-photo">
                                @if ($photo = $p['photo_url'] ?? null)
                                    <img src="{{ $photo }}" {{ \App\Support\Img::attrs($photo) }} alt="{{ $p['name'] }}" loading="lazy" decoding="async" style="{{ $p['photo_style'] ?? '' }}">
                                @else
                                    <span aria-hidden="true">{{ collect(explode(' ', $p['name']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}</span>
                                @endif
                            </div>
                            <div class="crew-info">
                                <h3>{{ $p['name'] }}</h3>
                                <p class="crew-role">{{ $p['role'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($teamUrl)
                <div class="crew-more" data-reveal>
                    <a href="{{ $teamUrl }}" class="btn btn-outline">{{ __('See more') }}</a>
                </div>
            @endif
        </div>
    </section>
    @endif

    @if (count($clients))
    <section class="sec {{ $nextTone() }}" aria-labelledby="clients-title">
        <div class="container clients" data-reveal>
            <div class="sec-head">
                <span class="eyebrow">{{ __('Clients') }}</span>
                <h2 id="clients-title">{{ __('Who we have worked with') }}</h2>
            </div>
            <div class="client-row">
                @foreach ($clients as [$cn, $cf])
                    @if ($cf)
                        <img src="{{ $cf }}" {{ \App\Support\Img::attrs($cf) }} alt="{{ $cn }}" loading="lazy" decoding="async">
                    @else
                        <span>{{ $cn }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (count($faqs))
    <section class="sec {{ $nextTone() }}" id="faq" aria-labelledby="faq-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">FAQ</span>
                <h2 id="faq-title">{{ __('Questions?') }} <span class="accent">{{ __('Look here.') }}</span></h2>
            </div>

            <div class="faq" data-reveal>
                <div class="faq-list">
                    @foreach ($faqs as $f)
                        <details class="faq-item" name="faq" @if ($loop->first) open @endif>
                            <summary>{{ $f['q'] }}</summary>
                            <p>{{ $f['a'] }}</p>
                        </details>
                    @endforeach
                </div>

                <aside class="faq-aside">
                    <h3>{{ __('Have a different question?') }}</h3>
                    <p>{{ __('Tell us what you need and talk to us directly by phone.') }}</p>
                    <a href="{{ $contactUrl }}" class="btn btn-primary">{{ __('Contact us') }}</a>
                </aside>
            </div>
        </div>
    </section>
    @endif

</div>
@endsection