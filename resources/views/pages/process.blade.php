@extends('layouts.site')

@section('title', __('Our process'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">Our work process</span>
            <h1>Only six stages, maximum results.</h1>
            <p class="lead">Storyboard, animatic, compositing, and grading are no longer separate stages. Layout is done in 3D, complete with timing and voice.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            @if ($stages->isEmpty())
                <p class="pg-empty">{{ __('The process will be described here soon.') }}</p>
            @else
                <ol class="pg-steps">
                    @foreach ($stages as $s)
                        <li class="pg-step" data-reveal>
                            <div>
                                <h2>{{ $s->title }}</h2>
                                @if ($s->summary)<p>{{ $s->summary }}</p>@endif
                                @if ($s->details)<p>{!! nl2br(e($s->details)) !!}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif

            <div class="pg-actions">
                <a href="#contact" class="btn btn-primary">{{ __('Talk to us about your project') }}</a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline">{{ __('See our projects') }}</a>
            </div>
        </div>
    </section>
@endsection