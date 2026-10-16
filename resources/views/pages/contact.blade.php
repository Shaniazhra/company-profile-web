@extends('layouts.site')

@section('title', __('Contact'))

@push('head')
    @include('partials.page-styles')
@endpush

@section('content')
    <section class="page-hero">
        <div class="container" data-reveal>
            <span class="eyebrow">Contact</span>
            <h1>Tell us about your project</h1>
            <p class="lead">Send a message or call one of us directly.</p>
        </div>
    </section>
    <section class="section">
        <div class="container pg-split">
            <div data-reveal>
                @if ($people->isNotEmpty())
                    <ul class="pg-contact">
                        @foreach ($people as $p)
                            <li class="pg-card">
                                <h2>{{ $p->name }}</h2>
                                @if ($p->role)<p>{{ $p->role }}</p>@endif
                                @if ($p->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $p->phone) }}" class="pg-more">{{ $p->phone }}</a>@endif
                                @if ($p->email)<a href="mailto:{{ $p->email }}" class="pg-more">{{ $p->email }}</a>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="pg-actions"><a class="pg-more" href="https://instagram.com/dream_ratio" target="_blank" rel="noopener">Instagram @dream_ratio</a></p>
            </div>

            <div data-reveal id="form">
                @if (session('sent'))
                    <p class="pg-alert" role="status">{{ __('Your message was sent.') }}</p>
                @endif

                <form class="pg-form" method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    <div class="pg-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                    <div class="pg-field">
                        <label for="c-name">{{ __('Your name') }}</label>
                        <input id="c-name" name="name" type="text" value="{{ old('name') }}" maxlength="120" required autocomplete="name" @error('name') aria-invalid="true" aria-describedby="c-name-err" @enderror>
                        @error('name')<p class="pg-err" id="c-name-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="pg-field">
                        <label for="c-contact">{{ __('Your email or phone number') }}</label>
                        <input id="c-contact" name="contact" type="text" value="{{ old('contact') }}" maxlength="190" required @error('contact') aria-invalid="true" aria-describedby="c-contact-err" @enderror>
                        @error('contact')<p class="pg-err" id="c-contact-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="pg-field">
                        <label for="c-message">{{ __('What do you need?') }}</label>
                        <textarea id="c-message" name="message" maxlength="3000" required @error('message') aria-invalid="true" aria-describedby="c-message-err" @enderror>{{ old('message') }}</textarea>
                        @error('message')<p class="pg-err" id="c-message-err">{{ $message }}</p>@enderror
                    </div>

                    <div><button type="submit" class="btn btn-primary">{{ __('Send message') }}</button></div>
                </form>
            </div>
        </div>
    </section>
@endsection