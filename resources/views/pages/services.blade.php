@extends('layouts.site')

@section('title', __('Services'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">What we do</span>
            <h1>Services</h1>
            <p class="lead">Choose a service to see how we work on it.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            @if ($services->isEmpty())
                <p class="pg-empty">{{ __('Services will be listed here soon.') }}</p>
            @else
                <div class="pg-grid">
                    @foreach ($services as $s)
                        <a class="pg-card" href="{{ route('services.show', $s->id) }}" data-reveal>
                            @if ($s->image)<div class="pg-media"><img src="{{ asset('storage/'.$s->image) }}" alt="" loading="lazy"></div>@endif
                            <h2>{{ $s->title }}</h2>
                            <p>{{ $s->summary }}</p>
                            <span class="pg-more">{{ __('Learn more') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection