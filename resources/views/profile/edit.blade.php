<x-admin-layout>

    <style>
        .ac-page { width: 100%; max-width: 48rem; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .ac-page > * + * { margin-top: 1.5rem; }
        .ac-page h2 { margin: 0; font-size: var(--fs-h3); }
        .ac-intro { margin: 0.25rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        .ac-card { scroll-margin-top: 5.5rem; overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .ac-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .ac-head h3 { margin: 0; font-size: var(--fs-h4); }
        .ac-head p { margin: 0.15rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }
        .ac-body { display: grid; gap: 1rem; padding: 1.5rem; }
        .ac-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 0.75rem; padding: 0.85rem 1.5rem; border-top: 1px solid var(--color-border); background: color-mix(in srgb, var(--color-primary) 4%, white); }

        .ac-label { display: block; margin-bottom: 0.35rem; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
        .ac-input { width: 100%; padding: 0.65rem 0.85rem; font-family: var(--font-body); font-size: var(--fs-body); color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); transition: border-color 0.2s, box-shadow 0.2s; }
        .ac-input:focus { outline: 0; border-color: var(--color-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 18%, transparent); }
        .ac-error { margin: 0.3rem 0 0; font-size: var(--fs-caption); color: var(--color-danger); }
        .ac-hint { margin: 0.3rem 0 0; font-size: var(--fs-caption); color: var(--color-muted); }
        .ac-note { display: inline-flex; align-items: center; gap: 0.4rem; margin: 0 auto 0 0; font-size: var(--fs-small); font-weight: 500; color: #1d7a55; }

        @media (max-width: 640px) {
            .ac-head, .ac-body, .ac-foot { padding-inline: 1rem; }
            .ac-foot .btn { width: 100%; }
        }
    </style>

    @php
        // The success message only shows on the card that was just saved.
        $flash = fn (string $key) => session('section') === $key ? session('status') : null;
        $profileErrors = $errors->getBag('updateProfile');
        $passwordErrors = $errors->getBag('updatePassword');
    @endphp

    <div class="ac-page">
        <div>
            <h2>Account</h2>
            <p class="ac-intro">Your own login details for the admin area.</p>
        </div>

        {{-- ===== Name and email ===== --}}
        <section id="sec-profile" class="ac-card">
            <form method="POST" action="{{ route('admin.account.profile') }}">
                @csrf
                @method('PUT')

                <div class="ac-head">
                    <h3>Name and email</h3>
                    <p>The email is what you use to log in.</p>
                </div>

                <div class="ac-body">
                    <div>
                        <label class="ac-label" for="name">Name</label>
                        <input id="name" name="name" type="text" class="ac-input" maxlength="255" required autocomplete="name"
                               value="{{ old('name', $user->name) }}">
                        @if ($profileErrors->has('name'))<p class="ac-error" role="alert">{{ $profileErrors->first('name') }}</p>@endif
                    </div>
                    <div>
                        <label class="ac-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="ac-input" maxlength="255" required autocomplete="username"
                               value="{{ old('email', $user->email) }}">
                        @if ($profileErrors->has('email'))<p class="ac-error" role="alert">{{ $profileErrors->first('email') }}</p>@endif
                    </div>
                </div>

                <div class="ac-foot">
                    @if ($msg = $flash('profile'))
                        <p class="ac-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </section>

        {{-- ===== Password ===== --}}
        <section id="sec-password" class="ac-card">
            <form method="POST" action="{{ route('admin.account.password') }}">
                @csrf
                @method('PUT')

                <div class="ac-head">
                    <h3>Change password</h3>
                    <p>Use a long, unique password.</p>
                </div>

                <div class="ac-body">
                    <div>
                        <label class="ac-label" for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" class="ac-input" required autocomplete="current-password">
                        @if ($passwordErrors->has('current_password'))<p class="ac-error" role="alert">{{ $passwordErrors->first('current_password') }}</p>@endif
                    </div>
                    <div>
                        <label class="ac-label" for="password">New password</label>
                        <input id="password" name="password" type="password" class="ac-input" required autocomplete="new-password">
                        @if ($passwordErrors->has('password'))<p class="ac-error" role="alert">{{ $passwordErrors->first('password') }}</p>@endif
                    </div>
                    <div>
                        <label class="ac-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="ac-input" required autocomplete="new-password">
                    </div>
                </div>

                <div class="ac-foot">
                    @if ($msg = $flash('password'))
                        <p class="ac-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Change password</button>
                </div>
            </form>
        </section>
    </div>

</x-admin-layout>