@extends('layouts.site')

@section('title', __('Our team'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">Our team</span>
            <h1>The team behind Dream Ratio</h1>
            <p class="lead">An R&D team with decades of experience in the animation industry.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            @if ($members->isEmpty())
                <p class="pg-empty">{{ __('Our team will be listed here soon.') }}</p>
            @else
                <div class="pg-grid">
                    @foreach ($members as $m)
                        <article class="pg-card" data-reveal>
                            <div class="pg-photo">
                                @if ($m->photo_url)<img src="{{ $m->photo_url }}" alt="{{ $m->name }}" loading="lazy">@else<span aria-hidden="true">{{ collect(explode(' ', $m->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}</span>@endif
                            </div>
                            <h2>{{ $m->name }}</h2>
                            <p>{{ $m->role }}</p>
                            @if ($m->creditList())
                                <ul class="pg-list">
                                    @foreach ($m->creditList() as $credit)<li>{{ $credit }}</li>@endforeach
                                </ul>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection