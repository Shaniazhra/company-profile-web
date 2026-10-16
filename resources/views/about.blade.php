@extends('layouts.site')

@section('title', __('About Us'))

@section('content')

    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">{{ __('Who We Are') }}</span>
            <h1>{{ __('About Us') }}</h1>
            @if ($site->about_text)
                <p class="lead">{{ $site->about_text }}</p>
            @endif
        </div>
    </section>

    <section class="section">
        <div class="container split split--top">
            <div class="panel" data-reveal>
                <span class="eyebrow">{{ __('Impact') }}</span>
                <h2>{{ __('Faster, smarter, more efficient.') }}</h2>
                @if ($site->impact_statement)
                    <p>{{ $site->impact_statement }}</p>
                @endif
            </div>

            <ul class="checks" data-reveal>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Based in Jakarta, Indonesia') }}</h4>
                        <p>{{ __('An animation studio working with local and international partners.') }}</p>
                    </div>
                </li>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Creative R&D team') }}</h4>
                        <p>{{ __('Combining technology and art to find better ways of working.') }}</p>
                    </div>
                </li>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Decades of experience') }}</h4>
                        <p>{{ __('In the animation industry, from series to feature films.') }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    @include('partials.cta')

@endsection