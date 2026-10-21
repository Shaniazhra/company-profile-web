@extends('layouts.site')

@section('title', $project->title ?: $project->name)

{{-- Project page CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css. --}}
@push('head')
@php
    // Site icon: use the favicon column if set, otherwise the logo.
    $siteRow     = $site ?? \App\Models\SiteSetting::first();
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
    .pd {
        --paper: color-mix(in srgb, var(--color-primary) 9%, #fff);
        --ink: #1a1433;
        --ink-soft: #5d5873;
        --violet-deep: #5b1f8e;
        --line: rgba(26, 20, 51, 0.14);
        overflow-x: clip;
    }
    .pd [id] { scroll-margin-top: calc(var(--nav-height) + 1rem); }
    .pd h1, .pd h2, .pd h3 { text-wrap: balance; }
    .pd a:focus-visible { outline: 3px solid var(--violet-deep); outline-offset: 3px; }
    .pd .btn { min-height: 2.75rem; }

    /* Section tones: white and tint alternate */
    .pd-sec { padding-block: clamp(3rem, 7vw, 5.5rem); color: var(--ink); }
    /* Light sections: dark text, so the theme buttons and links are readable on white */
    .pd-white, .pd-paper { --color-text: var(--ink); --color-muted: var(--ink-soft); --color-border: var(--line); --focus: var(--violet-deep); }
    .pd-white { background: #fff; }
    .pd-paper { background: var(--paper); }
    .pd-sec h2 { margin: 0; font-size: var(--fs-h1); line-height: 1.15; letter-spacing: -0.02em; color: var(--ink); }
    .pd-sec .eyebrow { color: var(--color-primary); }
    .pd-head { max-width: 44rem; margin: 0 auto clamp(2rem, 5vw, 3rem); text-align: center; }
    .pd-head h2 { margin-top: 0.75rem; }

    /* Hero: the project image fills the top and dissolves into the white page at the bottom.
       The title and season/year sit on the faded part in dark text. */
    .pd-hero {
        position: relative;
        isolation: isolate;
        display: flex;
        overflow: hidden;
        margin-top: calc(var(--nav-height) * -1);
        padding: calc(var(--nav-height) + clamp(3rem, 8vw, 5.5rem)) 0 clamp(1.5rem, 4vw, 2.5rem);
        text-align: center;
        background: linear-gradient(180deg, #1a1140 0%, #120b2b 100%);
    }
    .pd-hero-bg { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; }
    /* Soft dark top so the breadcrumb stays readable, then the fade into the page color */
    .pd-hero::before { content: ""; position: absolute; inset: 0; z-index: -1; background: linear-gradient(180deg, rgba(18, 11, 43, 0.55) 0%, rgba(18, 11, 43, 0.12) 30%, rgba(18, 11, 43, 0) 50%); }
    .pd-hero::after { content: ""; position: absolute; inset: 0; z-index: -1; background: linear-gradient(180deg, rgba(255, 255, 255, 0) 15%, rgba(255, 255, 255, 0.3) 40%, rgba(255, 255, 255, 0.7) 62%, rgba(255, 255, 255, 0.94) 80%, #fff 92%); }
    .pd-hero .container { position: relative; z-index: 1; display: flex; flex-direction: column; width: 100%; }
    .pd-crumbs { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.5rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.9); }
    .pd-crumbs a { color: #fff; text-decoration: none; }
    .pd-crumbs a:hover { text-decoration: underline; }
    .pd-crumbs [aria-current] { font-weight: 600; color: #fff; }
    .pd-titlebox { margin-top: auto; padding-top: clamp(3.5rem, 10vw, 6rem); }
    .pd-hero h1 { max-width: 20ch; margin: 0 auto; font-size: var(--fs-display); font-weight: 700; line-height: 1.1; letter-spacing: -0.02em; color: var(--ink); }
    /* Season and year: plain text on one line, no pills */
    .pd-meta { margin: 0.9rem 0 0; font-size: var(--fs-small); font-weight: 600; letter-spacing: 0.04em; color: var(--ink-soft); }
    .pd-meta span + span::before { content: "\00B7"; margin-inline: 0.6rem; }
    .pd .pd-hero a:focus-visible { outline-color: #fff; }

    /* Video: a big card right under the hero. The YouTube thumbnail is shown first, the player loads on click. */
    .pd-video { padding-top: 0; padding-bottom: clamp(3rem, 7vw, 5rem); }
    /* Layout placeholder when the project has no YouTube link yet */
    .pd-player.is-empty { display: grid; place-items: center; align-content: center; gap: 1rem; text-align: center; color: #fff; background: linear-gradient(135deg, #1a1140 0%, #3b1a6e 100%); }
    .pd-player.is-empty .pd-play { position: static; transform: none; }
    .pd-player.is-empty p { margin: 0; font-size: var(--fs-small); font-weight: 600; color: rgba(255, 255, 255, 0.85); }

    /* Synopsis: one centered column */
    .pd-synopsis { max-width: 46rem; margin-inline: auto; }
    .pd-prose p { margin: 0; font-size: var(--fs-small); line-height: 1.75; color: var(--ink-soft); }
    .pd-prose p + p { margin-top: 0.9rem; }

    .pd-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 10rem), 1fr)); gap: 0.75rem; margin: 1.75rem 0 0; padding: 0; }
    .pd-fact { display: flex; align-items: center; gap: 0.75rem; min-width: 0; padding: 0.8rem 0.9rem; background: #fff; border: 1px solid var(--line); border-radius: var(--radius-lg); }
    .pd-paper .pd-fact { background: #fff; }
    .pd-fact .icon-box { flex: none; width: 2.5rem; height: 2.5rem; background: color-mix(in srgb, var(--color-primary) 12%, #fff); color: var(--violet-deep); }
    .pd-fact .icon-box svg { width: 1.2rem; height: 1.2rem; }
    .pd-fact div { min-width: 0; }
    .pd-fact dt { font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-soft); }
    .pd-fact dd { margin: 0.1rem 0 0; font-size: var(--fs-small); font-weight: 600; color: var(--ink); overflow-wrap: anywhere; }
    .pd-fact dd a { color: var(--violet-deep); text-decoration: none; }
    .pd-fact dd a:hover { text-decoration: underline; }

    /* Video player: shows the YouTube thumbnail with a play button. The real player loads only after a click. */
    .pd-player { position: relative; z-index: 1; aspect-ratio: 16 / 9; overflow: hidden; background: #000; border-radius: calc(var(--radius-lg) * 1.5); box-shadow: 0 24px 40px -28px rgba(36, 26, 74, 0.65); }
    .pd-player-poster { position: absolute; inset: 0; display: block; color: #fff; }
    .pd-player-poster img { position: absolute; inset: 0; display: block; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .pd-player-poster::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(18, 11, 43, 0.05) 40%, rgba(18, 11, 43, 0.55) 100%); }
    .pd-player-poster:hover img { transform: scale(1.03); }
    .pd-play { position: absolute; top: 50%; left: 50%; z-index: 1; display: grid; place-items: center; width: clamp(2.75rem, 6vw, 3.5rem); aspect-ratio: 1; color: #fff; background: var(--color-primary); border-radius: 50%; box-shadow: 0 0 0 0.35rem rgba(255, 255, 255, 0.25); transform: translate(-50%, -50%); transition: transform 0.2s; }
    .pd-play svg { width: 42%; height: 42%; margin-left: 6%; fill: currentColor; }
    .pd-player-poster:hover .pd-play { transform: translate(-50%, -50%) scale(1.08); }
    .pd-player iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }

    /* Characters */
    .pd-chars { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 11rem), 1fr)); gap: 1.25rem; max-width: 64rem; margin: 0 auto; padding: 0; list-style: none; }
    .pd-char { display: flex; flex-direction: column; align-items: center; padding: 1.5rem 1rem 1.25rem; text-align: center; background: #fff; border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: 0 14px 28px -24px rgba(36, 26, 74, 0.5); transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .pd-char:hover { transform: translateY(-4px); box-shadow: 0 22px 34px -24px rgba(36, 26, 74, 0.55); }
    .pd-char-photo { display: grid; place-items: center; width: 7.5rem; aspect-ratio: 1; overflow: hidden; font-family: var(--font-heading); font-size: var(--fs-h1); font-weight: 600; color: #fff; background: var(--gradient-brand); border: 4px solid #fff; border-radius: 50%; box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 35%, #fff); }
    .pd-char-photo img { width: 100%; height: 100%; object-fit: cover; }
    .pd-char h3 { margin: 1.1rem 0 0.2rem; font-size: var(--fs-h4); color: var(--ink); }
    .pd-char p { margin: 0; font-size: var(--fs-small); font-weight: 600; color: var(--violet-deep); }

    /* More projects */
    .pd-more { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 11rem), 13.5rem)); justify-content: center; gap: 1.25rem; max-width: 64rem; margin-inline: auto; }
    .pd-more-card { display: block; color: inherit; text-decoration: none; }
    .pd-more-media { position: relative; aspect-ratio: 16 / 10; overflow: hidden; background: #26262c; border-radius: var(--radius); box-shadow: 0 18px 32px -26px rgba(36, 26, 74, 0.6); }
    .pd-more-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .pd-more-card:hover .pd-more-media img { transform: scale(1.04); }
    .pd-more-card h3 { margin: 0.7rem 0 0.1rem; font-size: var(--fs-small); font-weight: 700; line-height: 1.3; color: var(--ink); }
    .pd-more-card p { margin: 0; font-size: var(--fs-caption); color: var(--ink-soft); }
    .pd-more-link { display: inline-block; margin-top: 0.25rem; font-size: var(--fs-caption); font-weight: 600; color: var(--color-primary); }
    .pd-more-all { display: flex; justify-content: center; margin-top: clamp(2rem, 5vw, 3rem); }


    @media (prefers-reduced-motion: reduce) {
        .pd-more-media img, .pd-char, .pd-player-poster img, .pd-play { transition: none; }
        .pd-more-card:hover .pd-more-media img, .pd-char:hover, .pd-player-poster:hover img { transform: none; }
        .pd-player-poster:hover .pd-play { transform: translate(-50%, -50%); }
    }
</style>
@endpush

@section('content')
@php
    $label       = $project->title ?: $project->name;
    $loc         = app()->getLocale();
    $homeUrl     = Route::has('home') ? route('home', ['locale' => $loc]) : url('/');
    $projectsUrl = route('projects.index', ['locale' => $loc]);
    $artUrl      = $project->image_url;

    // YouTube: the embed uses the video id, the buttons use the link as it was saved.
    $ytId  = null;
    $ytUrl = $project->youtube_url ?: null;
    if ($ytUrl && preg_match('~(?:youtu\.be/|[?&]v=|/(?:embed|shorts|live|v)/)([A-Za-z0-9_-]{11})~', $ytUrl, $m)) {
        $ytId = $m[1];
    }

    $paragraphs = collect(preg_split('/\R{2,}/', trim((string) $project->synopsis)))->map('trim')->filter()->values();
    $characters = collect($project->characters ?? [])->filter(fn ($c) => ! empty($c['name'] ?? null))->values();

    // The video card is white, so the light sections after it start with the tint and then alternate.
    $lightIndex = 1;
    $tone       = function () use (&$lightIndex) { return ['pd-white', 'pd-paper'][$lightIndex++ % 2]; };
@endphp

<div class="pd">

    {{-- Hero: image that fades into the page, title and season/year below the fade --}}
    <section class="pd-hero {{ $artUrl ? 'has-image' : '' }}">
        @if ($artUrl)<img class="pd-hero-bg" src="{{ $artUrl }}" alt="">@endif
        <div class="container" data-reveal>
            <nav class="pd-crumbs" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ $homeUrl }}">{{ __('Home') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <a href="{{ $projectsUrl }}">{{ __('Projects') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span aria-current="page">{{ $label }}</span>
            </nav>

            <div class="pd-titlebox">
                <h1>{{ $label }}</h1>

                @if ($project->tag || $project->release_year)
                    <p class="pd-meta">
                        @if ($project->tag)<span>{{ $project->tag }}</span>@endif
                        @if ($project->release_year)<span>{{ $project->release_year }}</span>@endif
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Video: big card from the YouTube link. Without JavaScript the thumbnail opens YouTube. --}}
    <section class="pd-sec pd-white pd-video" id="watch" aria-label="{{ __('Video') }}">
        <div class="container" data-reveal>
            @if ($ytId)
                <div class="pd-player" data-yt="{{ $ytId }}" data-title="{{ $label }}">
                    <a class="pd-player-poster" href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Play video') }}: {{ $label }}">
                        <img src="https://i.ytimg.com/vi/{{ $ytId }}/maxresdefault.jpg" alt="" loading="lazy" onerror="this.onerror=null;this.src='https://i.ytimg.com/vi/{{ $ytId }}/hqdefault.jpg'">
                        <span class="pd-play" aria-hidden="true"><svg viewBox="0 0 24 24"><polygon points="7 4 20 12 7 20 7 4"/></svg></span>
                    </a>
                </div>
            @else
                {{-- No usable YouTube link: the card is still shown so the layout is visible. --}}
                <div class="pd-player is-empty">
                    <span class="pd-play" aria-hidden="true"><svg viewBox="0 0 24 24"><polygon points="7 4 20 12 7 20 7 4"/></svg></span>
                    <p>{{ $ytUrl ? __('This video link could not be read') : __('Video coming soon') }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Synopsis --}}
    <section class="pd-sec {{ $tone() }}" aria-labelledby="overview-title">
        <div class="container">
            <div class="pd-synopsis" data-reveal>
                <div class="pd-head">
                    <h2 id="overview-title">{{ __('Synopsis') }}</h2>
                </div>

                <div class="pd-prose">
                    @forelse ($paragraphs as $para)
                        <p>{{ $para }}</p>
                    @empty
                        <p>{{ __('Synopsis coming soon') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Characters --}}
    @if ($characters->isNotEmpty())
        <section class="pd-sec {{ $tone() }}" aria-labelledby="chars-title">
            <div class="container">
                <div class="pd-head" data-reveal>
                    <span class="eyebrow">{{ __('Cast') }}</span>
                    <h2 id="chars-title">{{ __('Characters') }}</h2>
                </div>
                <ul class="pd-chars" data-reveal>
                    @foreach ($characters as $c)
                        @php $cp = $c['photo'] ?? ($c['photo_path'] ?? null); @endphp
                        <li class="pd-char">
                            <div class="pd-char-photo">
                                @if ($cp)<img src="{{ asset('storage/'.$cp) }}" alt="{{ $c['name'] }}" loading="lazy">@else<span aria-hidden="true">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span>@endif
                            </div>
                            <h3>{{ $c['name'] }}</h3>
                            @if (! empty($c['role']))<p>{{ $c['role'] }}</p>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- More projects --}}
    @if ($others->isNotEmpty())
        <section class="pd-sec {{ $tone() }}" aria-labelledby="more-title">
            <div class="container">
                <div class="pd-head" data-reveal>
                    <span class="eyebrow">{{ __('Keep exploring') }}</span>
                    <h2 id="more-title">{{ __('More projects') }}</h2>
                </div>
                <div class="pd-more">
                    @foreach ($others as $o)
                        <a class="pd-more-card" href="{{ route('projects.show', ['locale' => $loc, 'project' => $o->slug]) }}" data-reveal>
                            <div class="pd-more-media">
                                @if ($o->image_url)<img src="{{ $o->image_url }}" alt="" loading="lazy">@endif
                            </div>
                            <h3>{{ $o->title ?: $o->name }}</h3>
                            @if ($o->tag)<p>{{ $o->tag }}</p>@endif
                            <span class="pd-more-link">{{ __('View project') }}</span>
                        </a>
                    @endforeach
                </div>
                <div class="pd-more-all">
                    <a href="{{ $projectsUrl }}" class="btn btn-outline">{{ __('All projects') }}</a>
                </div>
            </div>
        </section>
    @endif

<script>
    // Clicking the thumbnail loads the YouTube player in place and starts the video.
    document.querySelectorAll('[data-yt]').forEach(function (box) {
        var link = box.querySelector('.pd-player-poster');
        if (!link) { return; }
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var frame = document.createElement('iframe');
            frame.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(box.dataset.yt) + '?autoplay=1&rel=0';
            frame.title = box.dataset.title || 'Video';
            frame.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
            frame.allowFullscreen = true;
            frame.referrerPolicy = 'strict-origin-when-cross-origin';
            box.replaceChildren(frame);
        });
    });
</script>
</div>
@endsection