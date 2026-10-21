@extends('layouts.site')

@section('title', __('About Us'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) (($site->about_text ?? null) ?: 'Dream Ratio: We Animate Dreams')), 155))

{{-- About-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css. --}}
@push('head')
@php
    // Site icon: use the favicon column if set, otherwise the logo.
    $faviconPath = $site?->favicon ?: $site?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($site?->updated_at?->timestamp ?? 1)
        : null;
@endphp

@vite(['resources/css/theme.css'])

@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
@endif

<style>
    .about {
        --paper: color-mix(in srgb, var(--color-primary) 9%, #fff);
        --ink: #1a1433;
        --ink-soft: #5d5873;
        --violet-deep: #5b1f8e;
        --line: rgba(26, 20, 51, 0.14);
        /* Resolved here so dark sections keep the light muted text and focus ring. */
        --lilac: var(--color-muted);
        --focus-light: var(--focus);
        overflow-x: clip;
    }
    .about [id] { scroll-margin-top: calc(var(--nav-height) + 1rem); }
    .about h1, .about h2, .about h3 { text-wrap: balance; }
    .about a:focus-visible,
    .about button:focus-visible { outline: 3px solid var(--focus, var(--violet-deep)); outline-offset: 3px; }
    .about .btn { min-height: 2.75rem; }

    /* Section tones: tint and white alternate, one section is dark */
    .about-sec { padding-block: clamp(3rem, 7vw, 5.5rem); background: var(--bg); color: var(--color-text); }
    .about-sec--tight { padding-block: clamp(2rem, 5vw, 3.5rem); }
    .ab-paper, .ab-white {
        --color-text: var(--ink);
        --color-muted: var(--ink-soft);
        --color-border: var(--line);
        --focus: var(--violet-deep);
    }
    .ab-paper { --bg: var(--paper); }
    .ab-white { --bg: #fff; }
    .about-sec--dark {
        --bg: var(--color-surface);
        --color-text: #fff;
        --color-muted: var(--lilac);
        --line: rgba(255, 255, 255, 0.2);
        --focus: var(--focus-light);
    }
    .about-sec h2, .about-sec h3 { color: var(--color-text); }
    .about-sec h2 { margin: 0; font-size: var(--fs-h1); line-height: 1.12; letter-spacing: -0.02em; }
    .about-sec p { margin: 0; }
    .ab-paper .eyebrow, .ab-white .eyebrow { color: var(--color-primary); }
    .about-sec--dark .eyebrow { color: var(--focus-light); }
    .ab-accent { color: var(--color-primary); }
    .about-sec--dark .ab-accent { color: var(--focus-light); }

    /* Section heading: centered */
    .ab-head { max-width: 44rem; margin: 0 auto clamp(2rem, 5vw, 3.5rem); text-align: center; }
    .ab-head h2 { margin-top: 0.75rem; }
    .ab-head p { margin: 1rem auto 0; max-width: 40ch; }

    /* Small chip above a heading (like the reference) */
    .ab-chip {
        display: inline-block;
        padding: 0;
        font-size: var(--fs-caption);
        font-weight: 600;
        line-height: 1.4;
        color: var(--violet-deep);
    }

    /* Hero: a short banner. Dark photo (or the dark gradient), breadcrumb and title centered. */
    .ab-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin-top: calc(var(--nav-height) * -1);
        padding: calc(var(--nav-height) + clamp(2.5rem, 6vw, 4rem)) 0 clamp(2.5rem, 6vw, 4rem);
        text-align: center;
        background: linear-gradient(180deg, #1a1140 0%, #120b2b 100%);
    }
    .ab-hero-bg { position: absolute; inset: 0; z-index: -2; width: 100%; height: 100%; object-fit: cover; }
    .ab-hero.has-image::before { content: ""; position: absolute; inset: 0; z-index: -1; background: rgba(18, 11, 43, 0.8); }
    .ab-hero .container { position: relative; z-index: 1; }
    .ab-hero h1 { margin: 0.6rem 0 0; font-size: var(--fs-h1); font-weight: 700; line-height: 1.1; letter-spacing: -0.02em; color: #fff; }
    .ab-crumbs { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.5rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.85); }
    .ab-crumbs a { color: #fff; text-decoration: none; }
    .ab-crumbs a:hover { text-decoration: underline; }
    .ab-crumbs [aria-current] { font-weight: 600; color: #fff; }
    .about .ab-hero a:focus-visible { outline-color: #fff; }

    /* Company introduction: editorial text and an optional supporting studio photo */
    .ab-intro-top { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr); gap: clamp(2rem, 6vw, 4.5rem); align-items: center; }
    .ab-intro-top.is-single { grid-template-columns: minmax(0, 44rem); justify-content: start; }
    .ab-intro-top-text { max-width: 42rem; }
    .ab-intro-top-text h2 { margin: 1rem 0 1.1rem; }
    .ab-lead { font-size: var(--fs-small); line-height: 1.75; color: var(--color-muted); }
    .ab-intro-top-text .btn-row { margin-top: 1.75rem; }
    .ab-media { position: relative; aspect-ratio: 16 / 9; overflow: hidden; background: var(--color-surface); border-radius: var(--radius-lg); }
    .ab-media img { position: absolute; inset: 0; display: block; width: 100%; height: 100%; object-fit: cover; }
    /* Our story: photos on the left, text on the right */
    .ab-intro { display: grid; grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr); gap: clamp(2.5rem, 6vw, 5rem); align-items: center; }
    .ab-intro.is-single { grid-template-columns: minmax(0, 44rem); justify-content: center; }
    .ab-photos { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .ab-photos.has-1 { grid-template-columns: minmax(0, 1fr); max-width: 26rem; }
    .ab-photos figure { margin: 0; aspect-ratio: 3 / 4; overflow: hidden; background: var(--paper); border-radius: var(--radius-lg); }
    .ab-photos figure:nth-child(2) { margin-top: 2rem; }
    .ab-photos img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .ab-intro-text h2 { margin: 0.75rem 0 1rem; }
    .ab-paras p { font-size: var(--fs-small); line-height: 1.7; color: var(--color-muted); }
    .ab-paras p + p { margin-top: 0.9rem; }

    /* Mission and vision: two plain blocks under a hairline */
    .ab-mv { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 14rem), 1fr)); gap: 1.5rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--line); }
    .ab-mv h3 { margin: 0 0 0.6rem; font-size: var(--fs-h4); }
    .ab-mv p { font-size: var(--fs-small); line-height: 1.65; color: var(--color-muted); }
    .ab-list { display: grid; gap: 0.6rem; margin: 0; padding: 0; list-style: none; }
    .ab-list li { position: relative; padding-left: 1.1rem; font-size: var(--fs-small); line-height: 1.5; color: var(--color-text); }
    .ab-list li::before { content: ""; position: absolute; top: 0.55em; left: 0; width: 0.4rem; height: 0.4rem; border-radius: 50%; background: var(--color-primary); }
    .ab-intro-cta .btn-row, .ab-intro-text .btn-row { margin-top: 2rem; }

    /* Our approach: statement on the left, three facts on the right. No photo, no background box. */
    .ab-approach { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(2rem, 5vw, 4rem); align-items: start; }
    .ab-approach-text h2 { margin: 0.75rem 0 1rem; max-width: 16ch; }
    .ab-statement { max-width: 44ch; font-size: var(--fs-body); line-height: 1.7; color: var(--color-muted); }
    .ab-facts { margin: 0; padding: 0; list-style: none; border-top: 1px solid var(--line); }
    .ab-facts li { padding: 0.9rem 0; border-bottom: 1px solid var(--line); }
    .ab-facts h3 { margin: 0 0 0.25rem; font-size: var(--fs-h4); }
    .ab-facts p { font-size: var(--fs-small); line-height: 1.6; color: var(--color-muted); }

    /* Why choose us: four reasons in a row, same pattern as Home */
    .ab-reasons { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 13rem), 1fr)); gap: 2rem 1.5rem; max-width: 64rem; margin: 0 auto; padding: 0; list-style: none; }
    .ab-reason { text-align: center; }
    .ab-reason .icon-box { width: 3rem; height: 3rem; margin-inline: auto; background: color-mix(in srgb, var(--color-primary) 12%, #fff); color: var(--violet-deep); }
    .ab-reason .icon-box svg { width: 1.5rem; height: 1.5rem; }
    .ab-reason h3 { margin: 0.8rem 0 0.35rem; font-size: var(--fs-h4); }
    .ab-reason p { font-size: var(--fs-small); line-height: 1.55; color: var(--color-muted); }

    /* Innovation: the six stages on the dark surface */
    .ab-steps { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); max-width: 64rem; margin: 0 auto; padding: 0; list-style: none; }
    .ab-step { position: relative; padding-inline: 0.5rem; text-align: center; }
    .ab-step-dot {
        display: grid;
        place-items: center;
        width: 3.5rem;
        height: 3.5rem;
        margin: 0 auto 0.9rem;
        border-radius: 50%;
        background: var(--color-primary);
        box-shadow: 0 0 0 0.45rem rgba(255, 255, 255, 0.1);
        font-family: var(--font-heading);
        font-size: var(--fs-h4);
        font-weight: 600;
        color: #fff;
    }
    .ab-step:not(:last-child)::after { content: ""; position: absolute; top: 1.75rem; left: calc(50% + 2.5rem); width: calc(100% - 5rem); border-top: 2px solid rgba(255, 255, 255, 0.3); }
    .ab-step-name { font-family: var(--font-heading); font-size: var(--fs-h4); font-weight: 600; color: #fff; }
    .ab-results { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; max-width: 64rem; margin: clamp(2.5rem, 5vw, 3.5rem) auto 0; padding: 0; list-style: none; }
    .ab-results li { padding: 1.25rem 1.5rem; text-align: center; font-size: var(--fs-small); line-height: 1.55; color: #fff; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: var(--radius-lg); }

    /* Team: same card as the Home page. A tall purple tab on the left with the brand name running up it,
       the person in front, and a gradient behind the name and role. Transparent PNG cutouts work best. */
    .crew { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr)); gap: 1.5rem; max-width: 62rem; margin-inline: auto; }
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
    .crew-card h3 { margin: 0; font-size: var(--fs-h4); font-weight: 700; line-height: 1.2; color: #fff; }
    .crew-role { margin-top: 0.2rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.9); }
    /* Text shown instead of a photo until one is uploaded */
    .crew-ph { padding: 1rem 1rem 5rem; text-align: center; font-family: var(--font-body); font-size: var(--fs-small); font-weight: 600; }

    /* Clients */
    .ab-clients { text-align: center; }
    .ab-clients .ab-head { margin-bottom: clamp(2.75rem, 6vw, 4rem); }
    .ab-client-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 1.5rem 3.5rem; }
    .ab-client-row span { font-family: var(--font-heading); font-size: var(--fs-h3); font-weight: 600; color: var(--color-text); }
    .ab-client-row img { width: 11rem; height: 3.5rem; object-fit: contain; }

    /* Photo gallery: every 4 photos fill a block of 4 columns x 2 rows. The frames are fixed, the photos are cropped to fit. */
    .ab-gallery { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: clamp(7.5rem, 16vw, 12rem); grid-auto-flow: dense; gap: 0.75rem; max-width: 64rem; margin-inline: auto; }
    .ab-g-item { position: relative; display: block; width: 100%; height: 100%; margin: 0; padding: 0; overflow: hidden; border: 0; background: var(--paper); border-radius: var(--radius-lg); cursor: zoom-in; }
    .ab-g-item img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .ab-g-item:hover img { transform: scale(1.04); }
    .ab-g-item:focus-visible { outline: 3px solid var(--focus, var(--violet-deep)); outline-offset: 3px; }
    .ab-g-item:nth-child(4n+1) { grid-column: span 2; grid-row: span 2; }
    .ab-g-item:nth-child(4n+4) { grid-column: span 2; }
    /* Last photos of an unfinished block grow to fill the empty space */
    .ab-g-item:nth-child(4n+2):nth-last-child(2),
    .ab-g-item:nth-child(4n+3):last-child { grid-row: span 2; }
    .ab-g-item:nth-child(4n+2):last-child { grid-column: span 2; grid-row: span 2; }
    .ab-gallery.is-few { grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr)); grid-auto-rows: auto; max-width: 52rem; }
    .ab-gallery.is-few .ab-g-item { grid-column: auto; grid-row: auto; aspect-ratio: 4 / 3; }

    /* Lightbox */
    .ab-lightbox { width: min(96vw, 70rem); max-width: none; max-height: 94vh; padding: 0; overflow: visible; color: #fff; background: transparent; border: 0; }
    .ab-lightbox::backdrop { background: rgba(10, 6, 26, 0.9); }
    .ab-lb-figure { margin: 0; text-align: center; }
    .ab-lb-figure img { display: block; max-width: 100%; max-height: 80vh; margin-inline: auto; object-fit: contain; border-radius: var(--radius); }
    .ab-lb-figure figcaption { margin-top: 0.75rem; min-height: 1.25rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.85); }
    .ab-lb-btn { position: absolute; z-index: 2; display: grid; place-items: center; width: 2.75rem; height: 2.75rem; padding: 0; font-size: 1.5rem; line-height: 1; color: #fff; background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 50%; cursor: pointer; }
    .ab-lb-btn:hover { background: var(--color-primary); }
    .ab-lb-btn:focus-visible { outline: 3px solid var(--focus-light); outline-offset: 2px; }
    .ab-lb-close { top: -3.25rem; right: 0; }
    .ab-lb-prev { top: 40%; left: 0.5rem; }
    .ab-lb-next { top: 40%; right: 0.5rem; }
    .ab-lightbox.is-single .ab-lb-prev, .ab-lightbox.is-single .ab-lb-next { display: none; }

    /* Placeholder: shown with a short text when nothing is uploaded yet */
    .about .ab-ph {
        display: grid;
        place-items: center;
        padding: 1rem;
        text-align: center;
        background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 22%, #fff), color-mix(in srgb, var(--color-primary) 8%, #fff));
        color: var(--violet-deep);
        font-size: var(--fs-small);
        font-weight: 600;
        cursor: default;
    }
    .about .ab-ph::after { content: attr(data-label); }

    @media (max-width: 700px) {
        .ab-gallery { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ab-g-item, .ab-g-item:nth-child(4n+2):nth-last-child(2), .ab-g-item:nth-child(4n+3):last-child { grid-column: auto; grid-row: auto; }
        .ab-g-item:nth-child(4n+1), .ab-g-item:nth-child(4n+4) { grid-column: span 2; }
        .ab-g-item:nth-child(4n+2):last-child { grid-column: 1 / -1; grid-row: auto; }
        .ab-lb-prev { left: 0.25rem; }
        .ab-lb-next { right: 0.25rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ab-g-item img, .crew-photo img { transition: none; }
        .ab-g-item:hover img { transform: none; }
        .crew-card:hover .crew-photo img { transform: none; }
    }

    @media (max-width: 900px) {
        .ab-intro, .ab-approach, .ab-intro-top { grid-template-columns: 1fr; }
        .ab-intro.is-single, .ab-intro-top.is-single { grid-template-columns: 1fr; }
        .ab-photos { max-width: 32rem; width: 100%; margin-inline: auto; }
        .ab-steps { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.75rem 0; }
        .ab-step:not(:last-child)::after { display: none; }
    }
    @media (max-width: 700px) {
        .ab-results { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .ab-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
@endpush

@section('content')
@php
    $locale     = app()->getLocale();
    $homeUrl    = Route::has('home') ? route('home', ['locale' => $locale]) : url('/');
    // The contact band lives in the footer. If a separate contact page exists, it is used automatically.
    $contactUrl = Route::has('contact') ? route('contact', ['locale' => $locale]) : '#contact';

    // Data from AboutController. Everything is optional. When a value is missing, a placeholder is shown instead.
    $reasons  = $reasons  ?? [];
    $ours     = $ours     ?? [];
    $results  = $results  ?? [];
    $clients  = $clients  ?? [];
    // Team: same source as the Home page (Crew members in the admin), so one edit updates both.
    $team     = \App\Models\CrewMember::active()->ordered()->get()->map(fn ($m) => [
        'name'      => $m->name,
        'role'      => $m->role,
        'photo_url' => $m->photo_url,
    ])->all();
    $teamUrl  = $teamUrl  ?? null;
    $projectsUrl = $projectsUrl ?? null;

    // Placeholders: used only when real data is missing
    if (! count($reasons)) {
        $reasons = [
            ['icon' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>', 'title' => __('Reason one'),   'text' => __('Text not filled yet.')],
            ['icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>', 'title' => __('Reason two'),   'text' => __('Text not filled yet.')],
            ['icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/>', 'title' => __('Reason three'), 'text' => __('Text not filled yet.')],
            ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'title' => __('Reason four'),  'text' => __('Text not filled yet.')],
        ];
    }
    if (! count($ours)) {
        $ours = [__('Stage one'), __('Stage two'), __('Stage three'), __('Stage four'), __('Stage five'), __('Stage six')];
    }
    if (! count($results)) {
        $results = [__('Result not filled yet.'), __('Result not filled yet.'), __('Result not filled yet.')];
    }
    if (! count($clients)) {
        $clients = [[__('Client logo not uploaded yet'), null], [__('Client logo not uploaded yet'), null], [__('Client logo not uploaded yet'), null], [__('Client logo not uploaded yet'), null]];
    }

    // Photos uploaded in Site Settings. Every slot is optional.
    //   about_image   = banner background at the top
    //   about_image_4 = photo next to the intro text (Who We Are)
    //   about_image_2 and about_image_3 = the two photos next to the story
    $img = fn ($p) => $p ? (preg_match('#^(https?:)?//|^/#', $p) ? $p : asset('storage/'.$p)) : null;
    $heroPhoto   = $img($site->about_image ?? null);
    // Keyed by column name, so each photo can use its own saved position (imageStyle).
    $storyPhotos = collect(['about_image_2', 'about_image_3'])
        ->filter(fn ($f) => ! empty($site->{$f}))
        ->mapWithKeys(fn ($f) => [$f => $img($site->{$f})]);

    // Intro text and supporting photo. The button goes to Projects when available, otherwise Contact.
    $introTitle = __('About').' '.$brand;
    $aboutText  = $site->about_text ?: __('Text not filled yet.');
    $introMedia = $img($site->about_image_4 ?? null);
    $introUrl   = $projectsUrl ?: $contactUrl;
    $introLabel = $projectsUrl ? __('View projects') : __('Contact us');

    // Gallery photos that are switched on, in the order set in the admin. The section always shows.
    $galleryPhotos = ($site->gallery_enabled ?? true) ? \App\Models\AboutPhoto::active()->ordered()->take(4)->get() : collect();

    // Story, vision and mission text come from Site Settings.
    $storyParas   = collect(preg_split('/\R{2,}/', trim((string) $site->story)))->map('trim')->filter()->values();
    $missionItems = collect(preg_split('/\R+/', trim((string) $site->mission)))->map('trim')->filter()->values();
    $vision       = $site->vision;

    if ($storyParas->isEmpty())   { $storyParas   = collect([__('Story text not filled yet.')]); }
    if ($missionItems->isEmpty()) { $missionItems = collect([__('Mission not filled yet.')]); }
    if (! filled($vision))        { $vision       = __('Vision not filled yet.'); }
    if ($storyPhotos->isEmpty())  { $storyPhotos  = collect(['about_image_2' => null, 'about_image_3' => null]); }

    $hasText    = true;
    $impactText = $site->impact_statement ?: __('Text not filled yet.');

    // Light sections alternate white and tint on their own.
    $lightIndex = 0;
    $nextTone   = function () use (&$lightIndex) { return ['ab-white', 'ab-paper'][$lightIndex++ % 2]; };

    // Short facts under the approach statement.
    $facts = [
        ['title' => __('Based in Jakarta, Indonesia'), 'text' => __('An animation studio that blends art, creativity, innovation, and technology.')],
        ['title' => __('Creative R&D team'),           'text' => __('Technology and art working together to find better ways to produce animation.')],
        ['title' => __('Decades of experience'),       'text' => __('Our team has worked in the animation industry for decades.')],
    ];
@endphp

<div class="about">

    {{-- Hero: a short banner with the breadcrumb and the page title --}}
    <section class="ab-hero {{ $heroPhoto ? 'has-image' : '' }}">
        @if ($heroPhoto)
            <img class="ab-hero-bg" src="{{ $heroPhoto }}" alt="" style="{{ $site->imageStyle('about_image') }}">
        @endif

        <div class="container">
            <nav class="ab-crumbs" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ $homeUrl }}">{{ __('Home') }}</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span aria-current="page">{{ __('About Us') }}</span>
            </nav>
            <h1>{{ __('About Us') }}</h1>
        </div>
    </section>

    {{-- Company introduction: text and studio photo --}}
    <section class="about-sec {{ $nextTone() }}" id="about" aria-labelledby="intro-title">
        <div class="container">
            <div class="ab-intro-top">
                <div class="ab-intro-top-text" data-reveal>
                    <span class="ab-chip">{{ __('Who We Are') }}</span>
                    <h2 id="intro-title">{{ $introTitle }}</h2>
                    <p class="ab-lead">{{ $aboutText }}</p>
                    <div class="btn-row">
                        <a href="{{ $introUrl }}" class="btn btn-primary">{{ $introLabel }}</a>
                    </div>
                </div>

                <div class="ab-media {{ $introMedia ? '' : 'ab-ph' }}" @unless ($introMedia) data-label="{{ __('Photo not uploaded yet') }}" @endunless data-reveal>
                    @if ($introMedia)
                        <img src="{{ $introMedia }}" alt="" loading="lazy" style="{{ $site->imageStyle('about_image_4') }}">
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Our story: photos, story text, mission and vision --}}
    <section class="about-sec {{ $nextTone() }}" id="story" aria-labelledby="story-title">
        <div class="container">
            <div class="ab-intro {{ $storyPhotos->isEmpty() || ! $hasText ? 'is-single' : '' }}">
                <div class="ab-photos has-{{ $storyPhotos->count() }}" data-reveal>
                    @foreach ($storyPhotos as $field => $src)
                        <figure class="{{ $src ? '' : 'ab-ph' }}" @unless ($src) data-label="{{ __('Photo not uploaded yet') }}" @endunless>
                            @if ($src)<img src="{{ $src }}" alt="" loading="lazy" style="{{ $site->imageStyle($field) }}">@endif
                        </figure>
                    @endforeach
                </div>

                <div class="ab-intro-text" data-reveal>
                    <span class="eyebrow">{{ __('Our story') }}</span>
                    <h2 id="story-title">{{ __('The story behind') }} <span class="ab-accent">{{ $brand }}</span></h2>

                    <div class="ab-paras">
                        @foreach ($storyParas as $para)<p>{{ $para }}</p>@endforeach
                    </div>

                    <div class="ab-mv">
                        <div>
                            <h3>{{ __('Our mission') }}</h3>
                            <ul class="ab-list">
                                @foreach ($missionItems as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>

                        <div>
                            <h3>{{ __('Vision') }}</h3>
                            <p>{{ $vision }}</p>
                        </div>
                    </div>

                    @if ($teamUrl || $projectsUrl)
                        <div class="btn-row">
                            @if ($teamUrl)<a href="{{ $teamUrl }}" class="btn btn-primary">{{ __('Meet the team') }}</a>@endif
                            @if ($projectsUrl)<a href="{{ $projectsUrl }}" class="btn btn-outline">{{ __('View projects') }}</a>@endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Our approach --}}
    <section class="about-sec {{ $nextTone() }}" id="approach" aria-labelledby="approach-title">
        <div class="container">
            <div class="ab-approach-wrap" data-reveal>
                <div class="ab-approach">
                    <div class="ab-approach-text">
                        <span class="eyebrow">{{ __('Our approach') }}</span>
                        <h2 id="approach-title">{{ __('Faster, smarter,') }} <span class="ab-accent">{{ __('more efficient.') }}</span></h2>
                        <p class="ab-statement">{{ $impactText }}</p>
                    </div>

                    <ul class="ab-facts">
                        @foreach ($facts as $fact)
                            <li>
                                <h3>{{ $fact['title'] }}</h3>
                                <p>{{ $fact['text'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Why choose us --}}
    <section class="about-sec {{ $nextTone() }}" id="why-us" aria-labelledby="why-title">
        <div class="container">
            <div class="ab-head" data-reveal>
                <span class="eyebrow">{{ __('Why choose us') }}</span>
                <h2 id="why-title">{{ __('Why') }} <span class="ab-accent">{{ __('choose us') }}</span> {{ __('for your animation?') }}</h2>
            </div>

            <ul class="ab-reasons" data-reveal>
                @foreach ($reasons as $r)
                    <li class="ab-reason">
                        <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $r['icon'] !!}</svg></span>
                        <h3>{{ $r['title'] }}</h3>
                        <p>{{ $r['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Innovation: the work process --}}
    <section class="about-sec about-sec--dark" id="process" aria-labelledby="stages-title">
        <div class="container">
            <div class="ab-head" data-reveal>
                <span class="eyebrow">{{ __('Our innovation') }}</span>
                <h2 id="stages-title">{{ __('Only six stages,') }} <span class="ab-accent">{{ __('maximum results.') }}</span></h2>
            </div>

            <ol class="ab-steps" data-reveal>
                @foreach ($ours as $step)
                    <li class="ab-step">
                        <span class="ab-step-dot" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="ab-step-name">{{ $step }}</span>
                    </li>
                @endforeach
            </ol>

            <ul class="ab-results" aria-label="{{ __('The result') }}" data-reveal>
                @foreach ($results as $line)<li>{{ $line }}</li>@endforeach
            </ul>
        </div>
    </section>

    {{-- Photo gallery. Always shown. Placeholder boxes with text appear until photos are uploaded. --}}
    <section class="about-sec {{ $nextTone() }}" id="gallery" aria-labelledby="gallery-title">
        <div class="container">
            <div class="ab-head" data-reveal>
                <span class="eyebrow">{{ __('Gallery') }}</span>
                <h2 id="gallery-title">{{ __('Life at the studio') }}</h2>
            </div>

            <div class="ab-gallery {{ $galleryPhotos->isNotEmpty() && $galleryPhotos->count() < 4 ? 'is-few' : '' }}" data-reveal>
                @forelse ($galleryPhotos as $photo)
                    <button type="button" class="ab-g-item" data-ab-photo data-src="{{ $photo->url() }}" data-alt="{{ $photo->alt }}" aria-label="{{ __('Enlarge photo') }}{{ $photo->alt ? ': '.$photo->alt : '' }}">
                        <img src="{{ $photo->url() }}" alt="{{ $photo->alt }}" loading="lazy" decoding="async">
                    </button>
                @empty
                    @for ($i = 0; $i < 4; $i++)
                        <div class="ab-g-item ab-ph" data-label="{{ __('Photo not uploaded yet') }}"></div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    @if ($galleryPhotos->isNotEmpty())
    <dialog class="ab-lightbox" id="ab-lightbox" aria-label="{{ __('Photo viewer') }}">
        <button type="button" class="ab-lb-btn ab-lb-close" aria-label="{{ __('Close') }}">&times;</button>
        <button type="button" class="ab-lb-btn ab-lb-prev" aria-label="{{ __('Previous photo') }}">&lsaquo;</button>
        <figure class="ab-lb-figure">
            <img src="" alt="">
            <figcaption></figcaption>
        </figure>
        <button type="button" class="ab-lb-btn ab-lb-next" aria-label="{{ __('Next photo') }}">&rsaquo;</button>
    </dialog>
    @endif

    {{-- Team: same data as the Home page --}}
    @if (count($team))
    <section class="about-sec {{ $nextTone() }}" id="team" aria-labelledby="team-title">
        <div class="container">
            <div class="ab-head" data-reveal>
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
                                    <img src="{{ $photo }}" alt="{{ $p['name'] }}" loading="lazy">
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
        </div>
    </section>
    @endif

    {{-- Clients --}}
    <section class="about-sec {{ $nextTone() }}" aria-labelledby="clients-title">
        <div class="container ab-clients" data-reveal>
            <div class="ab-head">
                <span class="eyebrow">{{ __('Clients') }}</span>
                <h2 id="clients-title">{{ __('Who we have worked with') }}</h2>
            </div>
            <div class="ab-client-row">
                @foreach ($clients as [$cn, $cf])
                    @if ($cf)
                        <img src="{{ $cf }}" alt="{{ $cn }}" loading="lazy">
                    @else
                        <span>{{ $cn }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

<script>
    (function () {
        // Gallery lightbox
        var dialog = document.getElementById('ab-lightbox');
        var items = Array.prototype.slice.call(document.querySelectorAll('[data-ab-photo]'));
        if (!dialog || !items.length || typeof dialog.showModal !== 'function') { return; }

        var img = dialog.querySelector('img');
        var caption = dialog.querySelector('figcaption');
        var index = 0;

        function show(i) {
            index = (i + items.length) % items.length;
            img.src = items[index].dataset.src;
            img.alt = items[index].dataset.alt || '';
            caption.textContent = items[index].dataset.alt || '';
        }

        dialog.classList.toggle('is-single', items.length < 2);
        items.forEach(function (btn, i) {
            btn.addEventListener('click', function () { show(i); dialog.showModal(); });
        });
        dialog.querySelector('.ab-lb-close').addEventListener('click', function () { dialog.close(); });
        dialog.querySelector('.ab-lb-prev').addEventListener('click', function () { show(index - 1); });
        dialog.querySelector('.ab-lb-next').addEventListener('click', function () { show(index + 1); });
        // A click on the dark area around the photo closes the viewer.
        dialog.addEventListener('click', function (e) { if (e.target === dialog) { dialog.close(); } });
        dialog.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') { show(index - 1); }
            if (e.key === 'ArrowRight') { show(index + 1); }
        });
    })();
</script>

</div>
@endsection