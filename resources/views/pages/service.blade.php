@extends('layouts.site')

@section('title', $service->title)

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <a class="pg-back" href="{{ route('services.index') }}">&larr; {{ __('All services') }}</a>
            <span class="eyebrow">{{ __('Service') }}</span>
            <h1>{{ $service->title }}</h1>
            @if ($service->summary)<p class="lead">{{ $service->summary }}</p>@endif
        </div>
    </section>

    <section class="section">
        <div class="container pg-split">
            <div class="pg-prose" data-reveal>
                @if ($service->description)
                    {!! nl2br(e($service->description)) !!}
                @else
                    <p>{{ __('More details are coming soon.') }}</p>
                @endif
                <div class="pg-actions">
                    <a href="#contact" class="btn btn-primary">{{ __('Ask about this service') }}</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline">{{ __('All services') }}</a>
                </div>
            </div>
            @if ($service->image)
                <div class="pg-media" data-reveal><img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" loading="lazy"></div>
            @endif
        </div>
    </section>

    @if ($others->isNotEmpty())
        <section class="section">
            <div class="container">
                <h2 class="pg-sub" style="margin-top:0">{{ __('Other services') }}</h2>
                <div class="pg-grid">
                    @foreach ($others as $o)
                        <a class="pg-card" href="{{ route('services.show', $o->id) }}">
                            <h3>{{ $o->title }}</h3>
                            <p>{{ $o->summary }}</p>
                            <span class="pg-more">{{ __('Learn more') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection