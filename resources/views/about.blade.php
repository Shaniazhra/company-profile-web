@extends('layouts.site')

@section('title', __('About Us'))

@section('content')

    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">{{ __('Siapa Kami') }}</span>
            <h1>{{ __('About Us') }}</h1>
            @if ($site->about_text)
                <p class="lead">{{ $site->about_text }}</p>
            @endif
        </div>
    </section>

    <section class="section">
        <div class="container split split--top">
            <div class="panel" data-reveal>
                <span class="eyebrow">{{ __('Dampak') }}</span>
                <h2>{{ __('Lebih cepat, lebih cerdas, lebih hemat.') }}</h2>
                @if ($site->impact_statement)
                    <p>{{ $site->impact_statement }}</p>
                @endif
            </div>

            <ul class="checks" data-reveal>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Berbasis di Jakarta, Indonesia') }}</h4>
                        <p>{{ __('Studio animasi dengan jangkauan kerja sama lokal maupun internasional.') }}</p>
                    </div>
                </li>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Tim R&D kreatif') }}</h4>
                        <p>{{ __('Memadukan teknologi dan seni untuk mencari cara kerja yang lebih baik.') }}</p>
                    </div>
                </li>
                <li>
                    <span class="icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <div>
                        <h4>{{ __('Pengalaman puluhan tahun') }}</h4>
                        <p>{{ __('Di industri animasi, dari serial hingga film layar lebar.') }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    @include('partials.cta')

@endsection