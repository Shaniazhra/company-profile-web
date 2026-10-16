@extends('layouts.site')

@section('title', __('FAQ'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">FAQ</span>
            <h1>Frequently asked questions</h1>
            <p class="lead">Quick answers about how we work.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            @if ($faqs->isEmpty())
                <p class="pg-empty">{{ __('Questions and answers will be listed here soon.') }}</p>
            @else
                <div class="pg-faq">
                    @foreach ($faqs as $f)
                        <details name="faq" @if ($loop->first) open @endif>
                            <summary>{{ $f->question }}</summary>
                            <p>{!! nl2br(e($f->answer)) !!}</p>
                        </details>
                    @endforeach
                </div>
            @endif

            <div class="pg-actions">
                <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('Have a different question?') }}</a>
            </div>
        </div>
    </section>
@endsection