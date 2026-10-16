@extends('layouts.site')

@section('title', __('Home'))
@section('description', \Illuminate\Support\Str::limit(strip_tags(($site->heart_statement ?? null) ?: 'Dream Ratio: We Animate Dreams'), 155))

{{-- Home-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css --}}
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
    /* Purple, dark surface, and light text come from theme.css. Only the warm paper
       and ink colors for light sections are defined here, because theme.css has no light surface. */
    .home {
        --paper: #f5f2ec;
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

    .sec { padding-block: clamp(3.5rem, 8vw, 6.5rem); background: var(--bg); color: var(--color-text); }
    .sec--tight { padding-block: clamp(2.5rem, 5vw, 3.5rem); }
    .sec--cards { padding-top: 0; }
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
        padding: calc(var(--nav-height) + clamp(3rem, 8vw, 6rem)) 0 clamp(9rem, 16vw, 12rem);
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
    .home-hero::after {
        content: "";
        position: absolute;
        left: -8%;
        right: -8%;
        bottom: -1px;
        height: clamp(4rem, 9vw, 7rem);
        background: var(--paper);
        border-radius: 50% 50% 0 0 / 100% 100% 0 0;
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

    /* Three service cards overlap the bottom of the hero */
    .svc-wrap { position: relative; z-index: 3; margin-top: calc(clamp(5rem, 11vw, 8rem) * -1); }
    .svc { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    .svc-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 1.75rem;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: 0 24px 40px -30px rgba(20, 14, 48, 0.55);
        transition: border-color 0.2s;
    }
    .svc-card:hover { border-color: var(--color-primary); }
    .svc-card h3 { margin: 0.75rem 0 0.5rem; font-size: var(--fs-h3); line-height: 1.25; }
    .svc-card p { flex: 1; margin-bottom: 1.25rem; font-size: var(--fs-small); }
    .svc-card .btn { min-height: 2.75rem; }

    /* Work process: six numbered steps on one line, joined by a dashed connector */
    .steps { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); max-width: 64rem; margin: 0 auto; padding: 0; list-style: none; }
    .step { position: relative; padding-inline: 0.5rem; text-align: center; }
    .step-dot {
        display: grid;
        place-items: center;
        width: 3.5rem;
        height: 3.5rem;
        margin: 0 auto 0.9rem;
        border-radius: 50%;
        background: var(--color-primary);
        box-shadow: 0 0 0 0.45rem color-mix(in srgb, var(--color-primary) 14%, transparent);
        font-family: var(--font-heading);
        font-size: var(--fs-h4);
        font-weight: 600;
        color: #fff;
    }
    .step:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 1.75rem;
        left: calc(50% + 2.5rem);
        width: calc(100% - 5rem);
        border-top: 2px dashed color-mix(in srgb, var(--color-primary) 45%, transparent);
    }
    .step-name { font-family: var(--font-heading); font-size: var(--fs-h4); font-weight: 600; color: var(--color-text); }

    .results { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; max-width: 64rem; margin: clamp(2.5rem, 5vw, 3.5rem) auto 0; padding: 0; list-style: none; }
    .results li { padding: 1.25rem 1.5rem; text-align: center; font-size: var(--fs-small); color: var(--color-text); background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius-lg); }

    /* Why choose us: screenshots on the left, four reasons on the right */
    .why { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: clamp(1.5rem, 4vw, 3rem); align-items: center; }
    .why--solo { grid-template-columns: 1fr; }
    .why-media { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
    .why-shot { position: relative; margin: 0; }
    .why-shot:first-child { grid-column: 1 / -1; }
    .why-shot .frame-media { aspect-ratio: 16 / 10; background: var(--color-bg); }
    .why-shot:first-child .frame-media { aspect-ratio: 16 / 9; }
    .why-tag { position: absolute; left: 0.75rem; bottom: 0.75rem; padding: 0.25rem 0.7rem; font-size: var(--fs-caption); font-weight: 600; letter-spacing: 0.04em; color: #fff; background: rgba(15, 11, 36, 0.82); border-radius: var(--radius); }
    .why-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.75rem 1.5rem; margin: 0; padding: 0; list-style: none; }
    .why--solo .why-list { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .why-item .icon-box { background: color-mix(in srgb, var(--color-primary) 12%, #fff); color: var(--violet-deep); }
    .why-item h3 { margin: 0.8rem 0 0.35rem; font-size: var(--fs-h4); }
    .why-item p { font-size: var(--fs-small); }

    /* Latest project: image on the left, details on the right. Both columns share the same height,
       so the top and bottom edges line up. The image fills whatever height the text column needs. */
    .pj { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); gap: clamp(2rem, 6vw, 4.5rem); align-items: stretch; max-width: 62rem; margin-inline: auto; }
    .pj-media { position: relative; isolation: isolate; display: flex; }
    .pj-media::before, .pj-media::after { content: ""; position: absolute; z-index: -1; border-radius: var(--radius); background: var(--color-primary); }
    .pj-media::before { right: -0.9rem; bottom: -0.9rem; width: 5rem; height: 5rem; transform: rotate(10deg); }
    .pj-media::after { right: 2.6rem; bottom: -1.5rem; width: 2.25rem; height: 2.25rem; transform: rotate(-14deg); opacity: 0.35; }
    .pj .frame-media { flex: 1; aspect-ratio: auto; min-height: 16rem; border-radius: calc(var(--radius-lg) * 1.5); }
    .pj .frame-media img { object-position: top; }
    .pj-text { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; }
    .pj-text .pj-title { margin: 0 0 0.5rem; font-size: var(--fs-h1); line-height: 1.12; letter-spacing: -0.02em; }
    .pj-tag { font-size: var(--fs-small); font-weight: 600; color: var(--color-muted); }
    .pj-synopsis { max-width: 44ch; margin: 1rem 0 1.5rem; color: var(--color-muted); }

    /* Team: portrait cards with a tall tab on the left and the brand name running up the tab, faded behind the person.
       The notch at the top right is cut with a mask. Transparent PNG cutouts (images/team/name.png) give the full
       effect; regular photos work too and simply fill the card. Credits sit below each card. */
    .crew { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 17rem), 1fr)); gap: 1.5rem; max-width: 62rem; margin-inline: auto; }
    .crew-item { width: 100%; max-width: 24rem; margin-inline: auto; }
    .crew-card {
        --tab: 34%;
        --notch: 2.75rem;
        position: relative;
        isolation: isolate;
        aspect-ratio: 3 / 4;
        overflow: hidden;
        border-radius: 1.75rem;
        background: linear-gradient(170deg, var(--color-primary) 0%, var(--violet-deep) 100%);
        color: #fff;
        -webkit-mask: linear-gradient(#000 0 0) left top / var(--tab) 100% no-repeat,
                      linear-gradient(#000 0 0) left bottom / 100% calc(100% - var(--notch)) no-repeat;
        mask: linear-gradient(#000 0 0) left top / var(--tab) 100% no-repeat,
              linear-gradient(#000 0 0) left bottom / 100% calc(100% - var(--notch)) no-repeat;
    }
    .crew-mark {
        position: absolute;
        z-index: 0;
        top: 1rem;
        left: calc(var(--tab) / 2);
        transform: translateX(-50%) rotate(180deg);
        writing-mode: vertical-rl;
        font-family: var(--font-heading);
        font-size: clamp(1.6rem, 2.6vw, 2.1rem);
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
        color: #fff;
    }
    .crew-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; transition: transform 0.5s ease; }
    .crew-card:hover .crew-photo img { transform: scale(1.04); }
    .crew-card::after {
        content: "";
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 2;
        height: 48%;
        background: linear-gradient(to top, rgba(91, 31, 142, 0.96) 0%, rgba(91, 31, 142, 0.9) 24%, rgba(91, 31, 142, 0) 100%);
        pointer-events: none;
    }
    .crew-info { position: absolute; right: 1.25rem; bottom: 1.1rem; left: 1.25rem; z-index: 3; }
    .crew-card h3 { margin: 0; font-size: var(--fs-h3); line-height: 1.2; color: #fff; }
    .crew-role { margin-top: 0.2rem; font-size: var(--fs-small); color: rgba(255, 255, 255, 0.88); }
    .crew-points { margin: 1.1rem 0 0; padding: 0; list-style: none; font-size: var(--fs-small); color: var(--color-muted); }
    .crew-points li { position: relative; padding: 0.3rem 0 0.3rem 1.1rem; }
    .crew-points li::before { content: ""; position: absolute; left: 0; top: 0.9em; width: 0.4rem; height: 0.4rem; border-radius: 50%; background: var(--color-primary); }

    /* Clients */
    .clients { display: grid; justify-items: center; gap: 1.5rem; text-align: center; }
    .clients h2 { font-size: var(--fs-h4); }
    .client-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 1rem 3rem; }
    .client-row span { font-family: var(--font-heading); font-size: var(--fs-h3); font-weight: 600; color: var(--color-muted); }
    .client-row img { height: 2.25rem; width: auto; max-width: 100%; opacity: 0.75; filter: grayscale(1); }

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
        .why, .pj, .faq { grid-template-columns: 1fr; }
        .pj { max-width: 34rem; }
        .pj-media { display: block; }
        .pj .frame-media { aspect-ratio: 16 / 9; min-height: 0; }
        .pj-text { align-items: center; text-align: center; }
        .pj-text .btn-row { justify-content: center; }
        .faq { max-width: 40rem; }
    }
    @media (max-width: 900px) {
        .svc { grid-template-columns: 1fr; max-width: 30rem; }
        .steps { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.75rem 0; }
        .step:not(:last-child)::after { display: none; }
        .why--solo .why-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 760px) {
        .results { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .why-list, .why--solo .why-list { grid-template-columns: 1fr; }
        .pj-media::before { right: -0.5rem; bottom: -0.5rem; }
        .pj-media::after { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        .svc-card, .faq-item, .crew-photo img { transition: none; }
        .crew-card:hover .crew-photo img { transform: none; }
    }
</style>
@endpush

@section('content')
@php
    $img      = fn (string $p) => file_exists(public_path($p)) ? asset($p) : null;
    $heroImg  = !empty($site->hero_image) ? asset('storage/'.$site->hero_image) : null;
    $heart    = ($site->heart_statement ?? null)
        ?: __('An animation studio with a real-time pipeline. Directors guide actors live through motion capture, lighting is done in real time, and production has fewer stages.');
    $title    = ($site->tagline ?? null) ?: $brand;

    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="16" height="16"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

    // To be replaced with data from the admin panel (stage 5)
    $services = [
        ['title' => __('Motion capture animation'), 'text' => __('The director guides actors live, and character movement feels weighty.')],
        ['title' => __('Procedural set dressing'),  'text' => __('Sets are built with PCG. The process is like painting, and faster than placing each piece by hand.')],
        ['title' => __('Real-time lighting'),       'text' => __('Light is adjusted and the result shows right away, so there is less to render.')],
    ];

    // Screenshots: save in public/images/focus/ (png/jpg/jpeg/webp)
    $focusImg = function (string $base) {
        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $path = "images/focus/{$base}.{$ext}";
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    };

    $shots = [
        ['title' => __('Lighting'),  'img' => $focusImg('lighting')],
        ['title' => __('Set dress'), 'img' => $focusImg('set-dress')],
        ['title' => __('Render'),    'img' => $focusImg('render')],
    ];
    // The screenshot column shows only when at least one image has been uploaded.
    $hasShots = collect($shots)->contains(fn ($s) => !empty($s['img']));

    // Icons are inline Feather paths, drawn with currentColor.
    $reasons = [
        ['title' => __('Fewer stages'),       'text' => __('Ten traditional stages are cut to six, so work moves faster.'),
         'icon' => '<path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>'],
        ['title' => __('Real-time lighting'), 'text' => __('Light is adjusted and the result shows instantly, so there is less to render.'),
         'icon' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>'],
        ['title' => __('Fast rendering'),     'text' => __('One shot in 1 minute on 1 computer, down from 675 minutes.'),
         'icon' => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>'],
        ['title' => __('Experienced team'),   'text' => __('Our chairman has 20+ years in international animation.'),
         'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    ];

    // The first project in this list is shown as the featured project.
    // 'synopsis' = two or three short sentences, shown only for the featured project.
    $projects = [
        ['title' => 'Ibra',                  'tag' => 'Season 2',            'img' => 'images/projects/ibra.jpg',       'synopsis' => null],
        ['title' => 'Magic 5 The Animation', 'tag' => 'The Animation',       'img' => 'images/projects/magic5.jpg',     'synopsis' => null],
        ['title' => "Isra Mi'raj",           'tag' => __('In cinemas 2026'), 'img' => 'images/projects/isra-miraj.jpg', 'synopsis' => null],
    ];
    $featured     = $projects[0] ?? null;
    $others       = array_slice($projects, 1);
    $featuredImg  = $featured ? $img($featured['img']) : null;

    // Until the projects page exists, links go to /{locale}/projects. Once the 'projects' route exists, it is used automatically.
    $projectsRoute = collect(['projects', 'projects.index'])->first(fn ($name) => Route::has($name));
    $projectsUrl   = $projectsRoute ? route($projectsRoute, ['locale' => $loc]) : url($loc.'/projects');

    // Production stages in the Dream Ratio pipeline
    $ours = ['Concept', 'Layout', 'Animating', 'Lighting', 'Render', 'Final Editing'];

    $team = [
        ['name' => "Dawa'i Fathulwally", 'role' => 'Founder & CEO', 'photo' => 'images/team/dawai.jpg', 'points' => [
            'Operating Man of Manara Animation Studio', 'Visual Effect', 'Supervisor Asset', 'Lumine Studio, Little Giantz']],
        ['name' => 'Panjiasmoro', 'role' => 'Founder & COO', 'photo' => 'images/team/panjiasmoro.jpg', 'points' => [
            'Technical Director of Manara Animation Studio', 'Animation Studio Pipeline and Workflow', 'Latest System Development', 'Lumine Studio, Little Giantz']],
        ['name' => 'Aditya Triantoro', 'role' => 'Chairman', 'photo' => 'images/team/aditya.jpg', 'points' => [
            'Creator of the animated films Nussa & Rarra and Trungtung',
            '20+ years in international animation: Oddbods, Rabbids, Lego Star Wars, Rob the Robot',
            'Experience on Hollywood feature films such as X-Men and Thor']],
    ];
    // A transparent PNG cutout with the same name (images/team/dawai.png) is used first, then the listed photo.
    $teamPhoto = fn (string $p) => $img(preg_replace('/\.\w+$/', '.png', $p)) ?: $img($p);

    $clients  = [['Emtek', 'images/clients/emtek.png'], ['Manara Studios', 'images/clients/manara.png'], ['IDN', 'images/clients/idn.png']];

    $faqs = [
        ['q' => __('What does Dream Ratio do?'),
         'a' => __('We are an animation studio with a real-time pipeline. Directors guide actors live through motion capture, lighting is done in real time, and production has fewer stages.')],
        ['q' => __('How did you cut ten stages to six?'),
         'a' => __('Storyboard, animatic, compositing, and grading are no longer separate stages. Layout is done in 3D, complete with timing and voice.')],
        ['q' => __('How fast is rendering?'),
         'a' => __('One shot renders in 1 minute on 1 computer, down from 675 minutes before.')],
        ['q' => __('How long does a rough compile take?'),
         'a' => __('A rough compile of one episode is ready in 3 days.')],
        ['q' => __('How do I start a project?'),
         'a' => __('Call one of us using the numbers at the bottom of this page and tell us what you need.')],
    ];
@endphp

<div class="home">

    <section class="home-hero {{ $heroImg ? 'has-image' : '' }}">
        @if ($heroImg)
            <img class="home-hero-bg" src="{{ $heroImg }}" alt="">
        @endif

        <div class="container">
            <h1 class="home-title">{{ $title }}</h1>
            <p class="lead">{{ $heart }}</p>
            <div class="btn-row">
                <a href="#contact" class="btn btn-primary">{{ __('Contact us') }}</a>
                <a href="#projects" class="btn btn-ghost">{{ __('View projects') }}</a>
            </div>
        </div>
    </section>

    <section class="sec sec--cards tone-paper" id="services" aria-label="{{ __('Services') }}">
        <div class="container">
            <div class="svc-wrap">
                <div class="svc">
                    @foreach ($services as $s)
                        <article class="svc-card" data-reveal>
                            <h3>{{ $s['title'] }}</h3>
                            <p>{{ $s['text'] }}</p>
                            <a href="#contact" class="btn btn-outline btn-sm">{{ __('Ask about this service') }}</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="sec tone-white" id="process" aria-labelledby="stages-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Our work process') }}</span>
                <h2 id="stages-title">{{ __('Only six stages,') }} <span class="accent">{{ __('maximum results.') }}</span></h2>
            </div>

            <ol class="steps" data-reveal>
                @foreach ($ours as $step)
                    <li class="step">
                        <span class="step-dot" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="step-name">{{ $step }}</span>
                    </li>
                @endforeach
            </ol>

            <ul class="results" aria-label="{{ __('The result') }}" data-reveal>
                <li>{{ __('Lighting is done in real time, so rendering is much lighter.') }}</li>
                <li>{{ __('A rough compile of one episode is ready in 3 days.') }}</li>
                <li>{{ __('One shot renders in 1 minute on 1 computer.') }}</li>
            </ul>
        </div>
    </section>

    <section class="sec tone-paper" id="why-us" aria-labelledby="why-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Why choose us') }}</span>
                <h2 id="why-title">{{ __('Why') }} <span class="accent">{{ __('choose us') }}</span> {{ __('for your animation?') }}</h2>
            </div>

            <div class="why {{ $hasShots ? '' : 'why--solo' }}" data-reveal>
                @if ($hasShots)
                    <div class="why-media">
                        @foreach ($shots as $item)
                            <figure class="why-shot">
                                <div class="frame-media">
                                    @if ($item['img'])
                                        <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" loading="lazy">
                                    @else
                                        <span>{{ $item['title'] }}<small>{{ __('Screenshot coming soon') }}</small></span>
                                    @endif
                                </div>
                                <figcaption class="why-tag">{{ $item['title'] }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                @endif

                <ul class="why-list">
                    @foreach ($reasons as $r)
                        <li class="why-item">
                            <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $r['icon'] !!}</svg></span>
                            <h3>{{ $r['title'] }}</h3>
                            <p>{{ $r['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @if ($featured)
    <section class="sec tone-white" id="projects" aria-labelledby="project-title">
        <div class="container">
            <div class="sec-head" data-reveal>
                <span class="eyebrow">{{ __('Projects') }}</span>
                <h2 id="project-title">{{ __('Our') }} <span class="accent">{{ __('latest project') }}</span></h2>
            </div>

            <div class="pj">
                <div class="pj-media" data-reveal>
                    <div class="frame-media">
                        @if ($featuredImg)
                            <img src="{{ $featuredImg }}" alt="{{ $featured['title'] }}" loading="lazy">
                        @else
                            <span>{{ $featured['title'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="pj-text" data-reveal>
                    <h3 class="pj-title">{{ $featured['title'] }}</h3>
                    <p class="pj-tag">{{ $featured['tag'] }}</p>
                    <p class="pj-synopsis">{{ !empty($featured['synopsis']) ? $featured['synopsis'] : __('Synopsis coming soon') }}</p>

                    @if (count($others) && $projectsRoute)
                        <div class="btn-row">
                            <a href="{{ $projectsUrl }}" class="btn btn-primary">{{ __('View other projects') }} {!! $arrow !!}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="sec tone-paper" id="team" aria-labelledby="team-title">
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
                                @if ($photo = $teamPhoto($p['photo']))
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
                        <ul class="crew-points">
                            @foreach ($p['points'] as $pt)<li>{{ $pt }}</li>@endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sec sec--tight tone-white" aria-labelledby="clients-title">
        <div class="container clients" data-reveal>
            <h2 id="clients-title">{{ __('Our clients') }}</h2>
            <div class="client-row">
                @foreach ($clients as [$cn, $cf])
                    @if ($logo = $img($cf))
                        <img src="{{ $logo }}" alt="{{ $cn }}" loading="lazy">
                    @else
                        <span>{{ $cn }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="sec tone-paper" id="faq" aria-labelledby="faq-title">
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
                    <a href="#contact" class="btn btn-primary">{{ __('Contact us') }}</a>
                </aside>
            </div>
        </div>
    </section>

</div>
@endsection