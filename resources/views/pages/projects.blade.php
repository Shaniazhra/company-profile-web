@extends('layouts.site')

@section('title', __('Projects'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">Our work</span>
            <h1>Projects</h1>
            <p class="lead">Series and films made with our real-time pipeline.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            @if ($projects->isEmpty())
                <p class="pg-empty">{{ __('Projects will be listed here soon.') }}</p>
            @else
                <div class="pg-grid">
                    @foreach ($projects as $p)
                        <a class="pg-card" href="{{ route('projects.show', $p->slug) }}" data-reveal>
                            @if ($p->image_url)<div class="pg-media"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>@endif
                            <h2>{{ $p->title }}</h2>
                            <p>{{ collect([$p->tag, $p->release_year])->filter()->implode('  /  ') }}</p>
                            <span class="pg-more">{{ __('View project') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection