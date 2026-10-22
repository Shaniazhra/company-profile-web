@extends('layouts.site')

@section('title', __('Contact'))

{{-- Contact-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css. --}}
@push('head')
@php
    // Office on Google Maps: the pin position (latitude, longitude) and the link of the place.
    $mapLat  = '-6.3278392';
    $mapLng  = '106.7944364';
    $mapLink = 'https://www.google.com/maps/place/DreamRatio/@-6.3278392,106.7944364,17z';

    // Site icon: use the favicon column if set, otherwise the logo.
    $siteRow     = $site ?? \App\Models\SiteSetting::first();
    $faviconPath = $siteRow?->favicon ?: $siteRow?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($siteRow?->updated_at?->timestamp ?? 1)
        : null;
@endphp

@include('partials.page-styles')
@vite(['resources/css/theme.css'])

@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
@endif

<style>
    /* White page: the text colors are set here because the site default is light text on a dark page */
    /* Title only: white background, dark text */
    .ct-title { display: grid; place-items: center; padding: calc(var(--nav-height) + clamp(1rem, 2.5vw, 1.75rem)) 0 clamp(0.75rem, 2vw, 1.25rem); text-align: center; background: #fff; }
    .ct-title h1 { margin: 0 auto; max-width: 24ch; color: #1a1433; text-wrap: balance; }
    .ct-wrap { --color-text: #1a1433; --color-muted: #5d5873; --color-border: #e4e1ee; --focus: var(--color-primary); background: #fff; color: var(--color-text); }
    /* Keeps the gap between the title and the first card short. The site default section padding is much larger. */
    .ct-wrap.section { padding-top: 0; }
    .ct-panel { padding: clamp(1.25rem, 3vw, 2rem); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 14px 30px -26px rgba(36, 26, 74, 0.5); }
    .ct-panel + .ct-panel { margin-top: 1.25rem; }
    .ct-head { margin-bottom: 1.25rem; }
    .ct-head h2 { margin: 0; font-size: var(--fs-h3); color: var(--color-text); }
    .ct-head p { margin: 0.25rem 0 0; color: var(--color-muted); }

    .ct-cards { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr)); margin: 0; padding: 0; list-style: none; }
    .ct-card { display: flex; align-items: center; gap: 0.9rem; min-width: 0; padding: 0.9rem 1.1rem; color: var(--color-text); text-decoration: none; background: color-mix(in srgb, var(--color-primary) 5%, #fff); border: 1px solid var(--color-border); border-radius: var(--radius-lg); transition: border-color 0.2s, transform 0.2s; }
    a.ct-card:hover { border-color: var(--color-primary); transform: translateY(-2px); }
    a.ct-card:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
    .ct-icon { flex: none; display: grid; place-items: center; width: 2.75rem; height: 2.75rem; color: #fff; background: var(--gradient-brand, var(--color-primary)); border-radius: 50%; }
    .ct-icon svg { width: 1.2rem; height: 1.2rem; }
    .ct-text { min-width: 0; }
    .ct-label { display: block; font-size: var(--fs-caption); color: var(--color-muted); }
    .ct-value { display: block; overflow-wrap: anywhere; font-weight: 600; }

    .ct-split { display: grid; gap: 1.5rem; grid-template-columns: minmax(0, 1fr); align-items: stretch; }
    @media (min-width: 900px) { .ct-split { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }
    .ct-map { position: relative; min-height: 20rem; overflow: hidden; border: 1px solid var(--color-border); border-radius: var(--radius-lg); background: color-mix(in srgb, var(--color-primary) 6%, #fff); }
    .ct-map iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
    .ct-map-link { position: absolute; left: 0.75rem; bottom: 0.75rem; z-index: 1; }
    .ct-side { display: grid; gap: 1rem; align-content: center; padding: 1rem; }
    .ct-side h2 { margin: 0; font-size: var(--fs-h3); color: var(--color-text); }
    .ct-side p { margin: 0; color: var(--color-muted); }
    .ct-side .btn { justify-self: start; }

    .ct-form { display: grid; gap: 1rem; }
    .ct-field { display: grid; gap: 0.35rem; }
    .ct-field label { font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
    .ct-field input, .ct-field textarea { width: 100%; padding: 0.75rem 1rem; font: inherit; color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); }
    .ct-field textarea { min-height: 9rem; resize: vertical; }
    .ct-field input:focus-visible, .ct-field textarea:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 1px; }
    .ct-field [aria-invalid="true"] { border-color: var(--color-danger); }
    .ct-err { margin: 0; font-size: var(--fs-small); color: var(--color-danger); }
    .ct-ok { margin: 0 0 1rem; padding: 0.75rem 1rem; color: #1d7a55; background: #e2f4ec; border-radius: var(--radius); }
    .ct-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
    .ct-form .btn { width: 100%; justify-content: center; }

    @media (prefers-reduced-motion: reduce) {
        .ct-card { transition: none; }
        a.ct-card:hover { transform: none; }
    }
</style>
@endpush

@section('content')
    @php
        $phoneIcon  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>';
        $mailIcon   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>';
        $pinIcon    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>';
    @endphp

    <section class="ct-title">
        <div class="container">
            <h1>Get in touch, let us know how we can help</h1>
        </div>
    </section>

    <section class="section ct-wrap">
        <div class="container">
                <div class="ct-panel" data-reveal>
                    <ul class="ct-cards">
                        @foreach ($people as $p)
                            @if ($p->phone)
                                <li>
                                    <a class="ct-card" href="tel:{{ preg_replace('/[^\d+]/', '', $p->phone) }}">
                                        <span class="ct-icon" aria-hidden="true">{!! $phoneIcon !!}</span>
                                        <span class="ct-text"><span class="ct-label">{{ $p->name }}@if ($p->role) · {{ $p->role }}@endif</span><span class="ct-value">{{ $p->phone }}</span></span>
                                    </a>
                                </li>
                            @endif
                            @if ($p->email)
                                <li>
                                    <a class="ct-card" href="mailto:{{ $p->email }}">
                                        <span class="ct-icon" aria-hidden="true">{!! $mailIcon !!}</span>
                                        <span class="ct-text"><span class="ct-label">{{ $p->name }}@if ($p->role) · {{ $p->role }}@endif</span><span class="ct-value">{{ $p->email }}</span></span>
                                    </a>
                                </li>
                            @endif
                        @endforeach

                        <li>
                                <a class="ct-card" href="{{ $mapLink }}" target="_blank" rel="noopener">
                                    <span class="ct-icon" aria-hidden="true">{!! $pinIcon !!}</span>
                                    <span class="ct-text"><span class="ct-label">Our office</span><span class="ct-value">DreamRatio on Google Maps</span></span>
                                </a>
                            </li>
                    </ul>
                </div>

            <div class="ct-panel" data-reveal id="form">
                <div class="ct-split">
                    <div class="ct-map">
                            <iframe title="Map of our office" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&amp;z=17&amp;output=embed"></iframe>
                            <a class="btn btn-primary btn-sm ct-map-link" href="{{ $mapLink }}" target="_blank" rel="noopener">Open in Google Maps</a>
                    </div>

                    <div class="ct-formbox">
                        @if (session('sent'))
                            <p class="ct-ok" role="status">{{ __('Your message was sent.') }}</p>
                        @endif

                        <form class="ct-form" method="POST" action="{{ route('contact.store', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <div class="ct-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                            <div class="ct-field">
                                <label for="c-name">{{ __('Your name') }}</label>
                                <input id="c-name" name="name" type="text" placeholder="Your name" value="{{ old('name') }}" maxlength="120" required autocomplete="name" @error('name') aria-invalid="true" aria-describedby="c-name-err" @enderror>
                                @error('name')<p class="ct-err" id="c-name-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="ct-field">
                                <label for="c-contact">{{ __('Your email') }}</label>
                                <input id="c-contact" name="contact" type="text" placeholder="Your email" value="{{ old('contact') }}" maxlength="190" required @error('contact') aria-invalid="true" aria-describedby="c-contact-err" @enderror>
                                @error('contact')<p class="ct-err" id="c-contact-err">{{ $message }}</p>@enderror
                            </div>
                            <div class="ct-field">
                                <label for="c-message">{{ __('What do you need?') }}</label>
                                <textarea id="c-message" name="message" placeholder="Write something..." maxlength="3000" required @error('message') aria-invalid="true" aria-describedby="c-message-err" @enderror>{{ old('message') }}</textarea>
                                @error('message')<p class="ct-err" id="c-message-err">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Send message') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection