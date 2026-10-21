<!DOCTYPE html>
@php
    // Logo and site icon come from Site Settings, like the public site.
    $site  = \App\Models\SiteSetting::first();
    $brand = ($site?->site_name) ?: 'Dream Ratio';

    $faviconPath = $site?->favicon ?: $site?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($site?->updated_at?->timestamp ?? 1)
        : null;

    $homeUrl = Route::has('home') ? route('home', ['locale' => 'en']) : url('/');
@endphp
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('Confirm password') }} | {{ $brand }}</title>

    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/theme.css'])

    {{-- Login-only CSS. Colors, fonts, text sizes, radius, and buttons come from the tokens in theme.css. --}}
    <style>
        .lg {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: clamp(1rem, 4vw, 2rem);
            font-family: var(--font-body);
            color: var(--color-text);
            background:
                radial-gradient(60rem 30rem at 85% -10%, color-mix(in srgb, var(--color-primary) 35%, transparent), transparent 70%),
                radial-gradient(40rem 24rem at -10% 110%, color-mix(in srgb, var(--color-primary) 22%, transparent), transparent 70%),
                var(--color-bg);
        }
        .lg-box { width: min(100%, 26rem); }
        .lg-brand { display: flex; justify-content: center; margin-bottom: 1.5rem; }
        .lg-brand a { text-decoration: none; color: inherit; border-radius: var(--radius); }
        .lg-brand a:focus-visible { outline: 3px solid var(--focus, var(--color-primary)); outline-offset: 4px; }

        .lg-card {
            padding: clamp(1.5rem, 5vw, 2.25rem);
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 30px 60px -35px rgba(0, 0, 0, 0.8);
        }
        .lg-card h1 { margin: 0; font-family: var(--font-heading); font-size: var(--fs-h3); line-height: 1.2; color: var(--color-text); }
        .lg-sub { margin: 0.4rem 0 1.5rem; font-size: var(--fs-small); color: var(--color-muted); }

        .lg-status { margin: 0 0 1rem; padding: 0.75rem 1rem; font-size: var(--fs-small); color: #b8f0d6; background: rgba(29, 122, 85, 0.25); border: 1px solid rgba(110, 220, 170, 0.4); border-radius: var(--radius); }

        .lg-form { display: grid; gap: 1.1rem; }
        .lg-field { display: grid; gap: 0.4rem; }
        .lg-field label { font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
        .lg-input { position: relative; }
        .lg-input input {
            width: 100%;
            min-height: 2.9rem;
            padding: 0.7rem 1rem;
            font: inherit;
            color: var(--color-text);
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            transition: border-color 0.2s, background-color 0.2s;
        }
        .lg-input input::placeholder { color: color-mix(in srgb, var(--color-muted) 70%, transparent); }
        .lg-input input:hover { border-color: color-mix(in srgb, var(--color-primary) 60%, var(--color-border)); }
        .lg-input input:focus-visible { outline: 3px solid var(--focus, var(--color-primary)); outline-offset: 1px; border-color: transparent; }
        .lg-input input[aria-invalid="true"] { border-color: var(--color-danger, #ff7b7b); }
        .lg-input.has-toggle input { padding-right: 3.25rem; }
        .lg-toggle {
            position: absolute;
            top: 50%;
            right: 0.35rem;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            width: 2.4rem;
            height: 2.4rem;
            padding: 0;
            color: var(--color-muted);
            background: transparent;
            border: 0;
            border-radius: var(--radius);
            cursor: pointer;
        }
        .lg-toggle:hover { color: var(--color-text); }
        .lg-toggle:focus-visible { outline: 3px solid var(--focus, var(--color-primary)); outline-offset: 1px; }
        .lg-toggle svg { width: 1.2rem; height: 1.2rem; }
        .lg-err { margin: 0; font-size: var(--fs-small); color: var(--color-danger, #ff9a9a); }

        .lg-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem 1rem; }
        .lg-check { display: inline-flex; align-items: center; gap: 0.6rem; min-height: 2.25rem; font-size: var(--fs-small); color: var(--color-muted); cursor: pointer; }
        .lg-check input { width: 1.1rem; height: 1.1rem; accent-color: var(--color-primary); }
        .lg-link { display: inline-flex; align-items: center; min-height: 2.25rem; font-size: var(--fs-small); color: var(--color-muted); text-decoration: underline; text-underline-offset: 3px; border-radius: var(--radius); }
        .lg-link:hover { color: var(--color-text); }
        .lg-link:focus-visible { outline: 3px solid var(--focus, var(--color-primary)); outline-offset: 2px; }

        .lg-form .btn { width: 100%; justify-content: center; min-height: 2.9rem; }
        .lg-back { margin: 1.25rem 0 0; text-align: center; }

        @media (prefers-reduced-motion: reduce) {
            .lg-input input { transition: none; }
        }
    </style>
</head>
<body>
    <main class="lg">
        <div class="lg-box">
            <div class="lg-brand">
                <a href="{{ $homeUrl }}" aria-label="{{ $brand }}">
                    <x-brand-logo :site="$site" :brand="$brand" size="md" />
                </a>
            </div>

            <div class="lg-card">
                <h1>Confirm your password</h1>
                <p class="lg-sub">This is a secure area. Please confirm your password before continuing.</p>

                <form class="lg-form" method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="lg-field">
                        <label for="password">{{ __('Password') }}</label>
                        <div class="lg-input has-toggle">
                            <input id="password" type="password" name="password" placeholder="Your password"
                                   required autofocus autocomplete="current-password"
                                   @error('password') aria-invalid="true" aria-describedby="password-err" @enderror>
                            <button type="button" class="lg-toggle" data-toggle="password" aria-label="Show password" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')<p class="lg-err" id="password-err">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
                </form>
            </div>

            <p class="lg-back"><a class="lg-link" href="{{ $homeUrl }}">&larr; Back to the website</a></p>
        </div>
    </main>

    <script>
        (function () {
            document.querySelectorAll('[data-toggle]').forEach(function (button) {
                var input = document.getElementById(button.dataset.toggle);
                if (!input) { return; }
                button.addEventListener('click', function () {
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.setAttribute('aria-pressed', show ? 'true' : 'false');
                    button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            });
        })();
    </script>

</body>
</html>