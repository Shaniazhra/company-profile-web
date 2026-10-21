@props([
    'title',
    'description' => null,
    'path'        => null,
    'action',
    'x'           => 50,
    'y'           => 50,
    'message'     => null,
])

@php
    $src   = $path ? asset('storage/'.$path) : null;
    $fx    = max(0, min(100, (int) old('hero_focus_x', $x)));
    $fy    = max(0, min(100, (int) old('hero_focus_y', $y)));
    $error = $errors->first('hero_focus_x') ?: $errors->first('hero_focus_y');
@endphp

<section id="sec-hero_focus" class="site-card" data-focus>
    <form method="POST" action="{{ $action }}">
        @csrf
        @method('PUT')

        <div class="site-card-head">
            <h3>{{ $title }}</h3>
            @if ($description)<p>{{ $description }}</p>@endif
        </div>

        <div class="site-card-body">
            @if ($src)
                <div class="fp-modes" role="group" aria-label="Preview shape">
                    <button type="button" class="is-on" data-mode="desktop" aria-pressed="true">Desktop</button>
                    <button type="button" data-mode="phone" aria-pressed="false">Phone</button>
                </div>

                <div class="fp-stage is-desktop" tabindex="0" role="img"
                     aria-label="Hero background preview. Drag to choose which part stays in view, or use the arrow keys.">
                    <img src="{{ $src }}" alt="" style="object-position: {{ $fx }}% {{ $fy }}%">
                </div>

                <input type="hidden" name="hero_focus_x" value="{{ $fx }}">
                <input type="hidden" name="hero_focus_y" value="{{ $fy }}">

                <p class="img-up-hint">Drag the photo to choose which part stays in view. Switch between Desktop and Phone to check both. The photo itself is never cropped, so you can change this at any time.</p>
                <p class="fp-msg img-up-hint" role="status"></p>
                @if ($error)<p class="img-up-error text-red-600">{{ $error }}</p>@endif
            @else
                <p class="img-up-empty">Upload a hero background first, then choose which part of it stays in view.</p>
            @endif
        </div>

        @if ($src)
            <div class="site-card-foot">
                @if ($message)
                    <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $message }}</p>
                @endif
                <button type="submit" class="btn btn-primary btn-sm fp-save" disabled>Save position</button>
            </div>
        @endif
    </form>
</section>

@once
<style>
    .fp-modes { display: inline-flex; gap: 0.25rem; margin-bottom: 0.9rem; padding: 0.2rem; background: color-mix(in srgb, var(--color-primary) 8%, white); border-radius: 999px; }
    .fp-modes button { padding: 0.3rem 0.9rem; font: inherit; font-size: var(--fs-small); font-weight: 600; color: var(--color-muted); background: transparent; border: 0; border-radius: 999px; cursor: pointer; }
    .fp-modes button.is-on { color: var(--color-primary); background: #fff; box-shadow: 0 1px 3px rgba(36, 26, 74, 0.15); }

    .fp-stage { position: relative; width: 100%; margin-inline: auto; overflow: hidden; background: #1c1640; border: 1px solid var(--color-border); border-radius: var(--radius); cursor: grab; touch-action: none; user-select: none; }
    .fp-stage:active { cursor: grabbing; }
    .fp-stage:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
    .fp-stage.is-desktop { max-width: 44rem; aspect-ratio: 21 / 9; }
    .fp-stage.is-phone { max-width: 14rem; aspect-ratio: 3 / 5; }
    .fp-stage img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; -webkit-user-drag: none; }
    /* The same shade the home hero uses, so you can keep the subject away from the text. It is only a preview. */
    .fp-stage::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0.55) 55%, rgba(0, 0, 0, 0.25) 100%); pointer-events: none; }
    .fp-msg:empty { display: none; }
</style>

<script>
    /* Hero focus point. The photo is never changed. Dragging only changes which part of it stays in view
       (CSS object-position), and that position is what gets saved. */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-focus]').forEach(function (root) {
            var stage = root.querySelector('.fp-stage');
            var img = stage && stage.querySelector('img');
            if (!img) { return; }

            var inX = root.querySelector('[name=hero_focus_x]');
            var inY = root.querySelector('[name=hero_focus_y]');
            var save = root.querySelector('.fp-save');
            var msg = root.querySelector('.fp-msg');
            var pos = { x: Number(inX.value), y: Number(inY.value) };
            var clamp = function (n) { return Math.min(100, Math.max(0, n)); };

            function apply() {
                img.style.objectPosition = pos.x + '% ' + pos.y + '%';
                inX.value = Math.round(pos.x);
                inY.value = Math.round(pos.y);
                save.disabled = false;
            }

            // How many pixels of the photo stick out of the frame on each axis. That is how far it can move.
            function slack() {
                var nw = img.naturalWidth, nh = img.naturalHeight;
                if (!nw || !nh) { return { x: 0, y: 0 }; }
                var sw = stage.clientWidth, sh = stage.clientHeight;
                var s = Math.max(sw / nw, sh / nh);
                return { x: nw * s - sw, y: nh * s - sh };
            }

            function note() {
                var s = slack();
                if (!img.naturalWidth) { msg.textContent = ''; return; }
                if (s.x < 1 && s.y < 1) { msg.textContent = 'In this shape the photo fits the frame exactly, so there is nothing to move.'; }
                else if (s.x < 1) { msg.textContent = 'In this shape the photo can only move up and down.'; }
                else if (s.y < 1) { msg.textContent = 'In this shape the photo can only move left and right.'; }
                else { msg.textContent = ''; }
            }

            if (img.complete) { note(); } else { img.addEventListener('load', note); }

            root.querySelectorAll('[data-mode]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var phone = btn.dataset.mode === 'phone';
                    stage.classList.toggle('is-phone', phone);
                    stage.classList.toggle('is-desktop', !phone);
                    root.querySelectorAll('[data-mode]').forEach(function (b) {
                        var on = b === btn;
                        b.classList.toggle('is-on', on);
                        b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                    note();
                });
            });

            stage.addEventListener('pointerdown', function (event) {
                event.preventDefault();
                var s = slack();
                var start = { x: event.clientX, y: event.clientY, px: pos.x, py: pos.y };
                stage.setPointerCapture(event.pointerId);

                function move(e) {
                    if (s.x >= 1) { pos.x = clamp(start.px - (e.clientX - start.x) / s.x * 100); }
                    if (s.y >= 1) { pos.y = clamp(start.py - (e.clientY - start.y) / s.y * 100); }
                    apply();
                }
                function end() {
                    stage.removeEventListener('pointermove', move);
                    stage.removeEventListener('pointerup', end);
                    stage.removeEventListener('pointercancel', end);
                }
                stage.addEventListener('pointermove', move);
                stage.addEventListener('pointerup', end);
                stage.addEventListener('pointercancel', end);
            });

            stage.addEventListener('keydown', function (event) {
                var step = { ArrowLeft: [5, 0], ArrowRight: [-5, 0], ArrowUp: [0, 5], ArrowDown: [0, -5] }[event.key];
                if (!step) { return; }
                event.preventDefault();
                var s = slack();
                if (s.x >= 1) { pos.x = clamp(pos.x + step[0]); }
                if (s.y >= 1) { pos.y = clamp(pos.y + step[1]); }
                apply();
            });
        });
    });
</script>
@endonce