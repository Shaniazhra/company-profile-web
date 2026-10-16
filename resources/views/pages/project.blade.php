@extends('layouts.site')

@section('title', $project->title)

@push('head')
    @include('partials.page-styles')
@endpush

@php
    $yt = null;
    if ($project->youtube_url && preg_match('~(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]{11})~', $project->youtube_url, $m)) {
        $yt = $m[1];
    }
    $characters = collect($project->characters ?? [])->filter(fn ($c) => ! empty($c['name'] ?? null));
@endphp

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <a class="pg-back" href="{{ route('projects.index') }}">&larr; {{ __('All projects') }}</a>
            <span class="eyebrow">{{ collect([$project->tag, $project->release_year])->filter()->implode('  /  ') ?: __('Project') }}</span>
            <h1>{{ $project->title }}</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="pg-split">
                <div class="pg-prose" data-reveal>
                    @if ($project->synopsis)
                        {!! nl2br(e($project->synopsis)) !!}
                    @else
                        <p>{{ __('Synopsis coming soon') }}</p>
                    @endif
                    <div class="pg-actions">
                        <a href="#contact" class="btn btn-primary">{{ __('Start your own project') }}</a>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline">{{ __('All projects') }}</a>
                    </div>
                </div>
                @if ($project->image_url)
                    <div class="pg-media" data-reveal><img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy"></div>
                @endif
            </div>

            @if ($yt)
                <h2 class="pg-sub">{{ __('Watch') }}</h2>
                <iframe class="pg-video" src="https://www.youtube-nocookie.com/embed/{{ $yt }}" title="{{ $project->title }}" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
            @endif

            @if ($characters->isNotEmpty())
                <h2 class="pg-sub">{{ __('Characters') }}</h2>
                <div class="pg-grid">
                    @foreach ($characters as $c)
                        @php $cp = $c['photo'] ?? ($c['photo_path'] ?? null); @endphp
                        <div class="pg-card">
                            <div class="pg-photo">
                                @if ($cp)<img src="{{ asset('storage/'.$cp) }}" alt="{{ $c['name'] }}" loading="lazy">@else<span aria-hidden="true">{{ mb_substr($c['name'], 0, 1) }}</span>@endif
                            </div>
                            <h3>{{ $c['name'] }}</h3>
                            @if (! empty($c['role']))<p>{{ $c['role'] }}</p>@endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($others->isNotEmpty())
                <h2 class="pg-sub">{{ __('More projects') }}</h2>
                <div class="pg-grid">
                    @foreach ($others as $o)
                        <a class="pg-card" href="{{ route('projects.show', $o->slug) }}">
                            <h3>{{ $o->title }}</h3>
                            <p>{{ $o->tag }}</p>
                            <span class="pg-more">{{ __('View project') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection