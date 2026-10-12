@extends('layouts.site')

@section('title', __('Alur Produksi'))

@section('content')

    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">{{ __('Alur Produksi') }}</span>
            <h1>{{ __('Dua cara membuat animasi') }}</h1>
            <p class="lead">{{ __('Perbandingan alur kerja animasi tradisional dengan alur Dream Ratio.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="track track--traditional" data-reveal>
                <div class="track-head">
                    <div>
                        <h2>{{ __('Cara Tradisional') }}</h2>
                        <p>{{ __('Memakan waktu lama dan anggaran besar') }}</p>
                    </div>
                    <span class="track-badge">{{ $traditional->count() }} {{ __('tahap') }}</span>
                </div>
                @include('partials.pipeline-steps', ['steps' => $traditional])
            </div>

            <div class="track track--ours" data-reveal>
                <div class="track-head">
                    <div>
                        <h2>{{ __('Cara Dream Ratio') }}</h2>
                        <p>{{ __('Lebih cepat dan efisien') }}</p>
                    </div>
                    <span class="track-badge">{{ $dreamRatio->count() }} {{ __('tahap') }}</span>
                </div>
                @include('partials.pipeline-steps', ['steps' => $dreamRatio])
            </div>
        </div>
    </section>

    @include('partials.cta')

@endsection