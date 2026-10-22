<x-admin-layout>
    <style>
        /* Same page padding, header, and card styles as the Projects page, so the admin feels like one product. */
        .pa { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .pa > * + * { margin-top: 1.5rem; }

        .pa-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-border); }
        .pa-head h2 { margin: 0; font-size: var(--fs-h3); }
        .pa-head p { margin: 0.25rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        .pa-card { overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .pa-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .pa-card-head h3 { margin: 0; font-size: var(--fs-h4); }
        .pa-card-head p { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }

        /* Hero photo of the public Team page */
        .pa-hero { display: grid; grid-template-columns: minmax(0, 36rem) minmax(0, 1fr); gap: 1.25rem 1.5rem; align-items: start; padding: 1.25rem 1.5rem; }
        .pa-frame { position: relative; display: grid; place-items: center; aspect-ratio: 9 / 2; overflow: hidden; background: color-mix(in srgb, var(--color-primary) 6%, white); border: 1px solid var(--color-border); border-radius: var(--radius); cursor: grab; touch-action: none; }
        .pa-frame:active { cursor: grabbing; }
        .pa-frame:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
        .pa-frame img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: var(--fx, 50%) var(--fy, 50%); transform: scale(var(--z, 1)); transform-origin: var(--fx, 50%) var(--fy, 50%); pointer-events: none; user-select: none; }
        .pa-frame-empty { font-size: var(--fs-small); color: var(--color-muted); }
        .pa-zoom { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; font-size: var(--fs-small); color: var(--color-muted); }
        .pa-zoom input[type="range"] { flex: 1; min-width: 0; accent-color: var(--color-primary); }
        .pa-zoom output { min-width: 3rem; text-align: right; font-variant-numeric: tabular-nums; }
        .pa-hero-buttons { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
        .pa-input { width: 100%; min-width: 12rem; padding: 0.5rem 0.75rem; font: inherit; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); }
        .pa-input:focus { outline: 0; border-color: var(--color-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 18%, transparent); }
        .pa-savebar { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 0.75rem; padding: 0.85rem 1.5rem; border-top: 1px solid var(--color-border); background: color-mix(in srgb, var(--color-primary) 4%, white); }
        .pa-savebar p { margin: 0 auto 0 0; font-size: var(--fs-small); color: var(--color-muted); }
        .pa-file { max-width: 100%; font-size: var(--fs-small); color: var(--color-muted); }
        .pa-file::file-selector-button { margin-right: 0.75rem; padding: 0.5rem 1rem; font: inherit; font-weight: 500; color: var(--color-primary); background: #fff; border: var(--border-width, 1px) solid var(--color-primary); border-radius: var(--radius); cursor: pointer; }
        .pa-file::file-selector-button:hover { color: #fff; background: var(--color-primary); }
        .pa-hint { margin: 0.75rem 0 0; font-size: var(--fs-caption); color: var(--color-muted); }
        .pa-error { margin: 0.5rem 0 0; font-size: var(--fs-small); color: var(--color-danger); }

        /* Team list: drag the handle to change the order */
        .tl { margin: 0; padding: 0; list-style: none; }
        .tl-item { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1rem; padding: 0.8rem 1.5rem; background: var(--color-surface); }
        .tl-item + .tl-item { border-top: 1px solid var(--color-border); }
        .tl-item.is-dragging { position: relative; z-index: 2; background: color-mix(in srgb, var(--color-primary) 8%, white); box-shadow: 0 14px 28px -18px rgba(36, 26, 74, 0.55); }
        .tl-handle { flex: none; display: grid; place-items: center; width: 2.25rem; height: 2.5rem; padding: 0; color: var(--color-muted); background: transparent; border: 0; border-radius: var(--radius); cursor: grab; touch-action: none; }
        .tl-handle:hover { color: var(--color-primary); background: color-mix(in srgb, var(--color-primary) 8%, white); }
        .tl-handle:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
        .tl-handle svg { width: 1.1rem; height: 1.1rem; }
        .is-dragging .tl-handle { cursor: grabbing; }
        .tl-photo { flex: none; display: grid; place-items: center; width: 3.5rem; height: 3.5rem; overflow: hidden; font-size: var(--fs-caption); color: var(--color-muted); background: color-mix(in srgb, var(--color-primary) 8%, white); border-radius: var(--radius); }
        .tl-photo img { width: 100%; height: 100%; object-fit: cover; }
        .tl-main { flex: 1 1 10rem; min-width: 0; }
        .tl-name { margin: 0; overflow: hidden; font-weight: 600; color: var(--color-text); text-overflow: ellipsis; white-space: nowrap; }
        .tl-role { margin: 0.1rem 0 0; overflow: hidden; font-size: var(--fs-small); color: var(--color-muted); text-overflow: ellipsis; white-space: nowrap; }
        .tl-pill { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.8rem; font-size: var(--fs-caption); font-weight: 600; color: #7a5a1d; background: #f7efd9; border-radius: 999px; }
        .tl-pill::before { content: ""; width: 0.5rem; height: 0.5rem; border-radius: 50%; background: currentColor; }
        .tl-pill.is-live { color: #1d7a55; background: #e2f4ec; }
        .tl-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .tl-actions form { margin: 0; }
        .tl-empty { padding: 3rem 1.25rem; text-align: center; color: var(--color-muted); }
        .tl-status { margin: 0; padding: 0.75rem 1.5rem; font-size: var(--fs-small); color: var(--color-muted); border-top: 1px solid var(--color-border); background: color-mix(in srgb, var(--color-primary) 4%, white); }
        .tl-status.is-ok { color: #1d7a55; }
        .tl-status.is-error { color: var(--color-danger); }

        /* Divisions */
        .dv-add { display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .dv-add .pa-input { flex: 1 1 14rem; }
        .dv-rename { display: flex; flex: 1 1 14rem; flex-wrap: wrap; gap: 0.5rem; margin: 0; min-width: 0; }
        .dv-rename .pa-input { flex: 1 1 10rem; }
        .dv-count { font-size: var(--fs-small); color: var(--color-muted); white-space: nowrap; }
        .dv-error { margin: 0; padding: 0.5rem 1.5rem 0; font-size: var(--fs-small); color: var(--color-danger); }
        .tl-div { color: var(--color-primary); font-weight: 600; }

        @media (max-width: 760px) {
            .pa-card-head, .tl-item, .tl-status, .dv-add { padding-inline: 1rem; }
            .pa-hero { grid-template-columns: 1fr; padding-inline: 1rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            .tl-item { transition: none; }
        }
    </style>

    <div class="pa">
        <div class="pa-head">
            <div>
                <h2>Team</h2>
                <p>People shown in the team section on the home page and on the Team page.</p>
            </div>
            <a href="{{ route('admin.team.create') }}" class="btn btn-primary btn-sm">+ Add Team member</a>
        </div>

        <x-admin.flash />

        {{-- Hero photo of the public Team page --}}
        @php $heroPath = \App\Models\SiteSetting::first()?->team_image; @endphp
        <section class="pa-card">
            <div class="pa-card-head">
                <h3>Page hero photo</h3>
                <p>The banner photo at the very top of the Team page on the website.</p>
            </div>
            @php
                $hx = (float) (\App\Models\SiteSetting::first()?->team_image_x ?? 50);
                $hy = (float) (\App\Models\SiteSetting::first()?->team_image_y ?? 50);
                $hz = (float) (\App\Models\SiteSetting::first()?->team_image_zoom ?? 1);
            @endphp
            <form method="POST" action="{{ route('admin.team.hero.update') }}" enctype="multipart/form-data" class="pa-hero" data-hero>
                @csrf
                @method('PUT')

                <div>
                    <div class="pa-frame" data-frame tabindex="0" role="group"
                         aria-label="Hero photo preview. Drag, or use the arrow keys, to choose which part stays in view."
                         style="--fx: {{ $hx }}%; --fy: {{ $hy }}%; --z: {{ $hz }};">
                        <img data-img src="{{ $heroPath ? asset('storage/'.$heroPath) : '' }}" alt="" draggable="false" @unless ($heroPath) hidden @endunless>
                        <span class="pa-frame-empty" data-empty @if ($heroPath) hidden @endif>No hero photo yet</span>
                    </div>
                    <div class="pa-zoom">
                        <label for="hero-zoom">Zoom</label>
                        <input type="range" id="hero-zoom" min="1" max="3" step="0.05" value="{{ $hz }}" data-zoom>
                        <output data-zoom-out>{{ round($hz * 100) }}%</output>
                        <button type="button" class="btn btn-outline btn-sm" data-reset>Reset</button>
                    </div>
                    <p class="pa-hint" style="margin-top: 0.5rem;">Drag the photo to choose which part stays in view. Slide to zoom in or out. On phones the banner is taller, so keep the subject near the middle.</p>
                    <input type="hidden" name="focus_x" value="{{ $hx }}" data-fx>
                    <input type="hidden" name="focus_y" value="{{ $hy }}" data-fy>
                    <input type="hidden" name="zoom" value="{{ $hz }}" data-z>
                </div>

                <div>
                    <input type="file" name="team_image" accept="image/png,image/jpeg,image/webp" class="pa-file" data-file aria-label="Choose the hero photo">
                    <div class="pa-hero-buttons">
                        <button type="submit" class="btn btn-primary btn-sm">Save hero photo</button>
                        @if ($heroPath)
                            <button type="submit" form="hero-remove" class="btn btn-outline btn-sm" onclick="return confirm('Remove the hero photo?');">Remove photo</button>
                        @endif
                    </div>
                    @error('team_image')<p class="pa-error" role="alert">{{ $message }}</p>@enderror
                    <p class="pa-hint">Wide landscape photo, at least 2000 px wide. PNG, JPG, or WebP, max 5 MB. The website darkens it so the white title stays readable. Empty shows the plain dark banner.</p>
                </div>
            </form>
            <form id="hero-remove" method="POST" action="{{ route('admin.team.hero.destroy') }}" hidden>@csrf @method('DELETE')</form>
        </section>

        {{-- Divisions of the team --}}
        <section class="pa-card">
            <div class="pa-card-head">
                <h3>Divisions</h3>
                <p>Group the people on the Team page, for example Animation or Production. Drag the handle to change the order.</p>
            </div>

            <form method="POST" action="{{ route('admin.team.divisions.store') }}" class="dv-add">
                @csrf
                <input type="text" name="name" class="pa-input" placeholder="New division name" maxlength="120" required aria-label="New division name" value="{{ old('name') }}">
                <button type="submit" class="btn btn-primary btn-sm">Add division</button>
            </form>
            @if ($errors->division->has('name'))<p class="dv-error" role="alert">{{ $errors->division->first('name') }}</p>@endif

            @if ($divisions->isEmpty())
                <p class="tl-empty">No divisions yet. Add one above, then choose it when you edit a person.</p>
            @else
                <ul class="tl" data-sortable data-url="{{ route('admin.team.divisions.order') }}" data-token="{{ csrf_token() }}">
                    @foreach ($divisions as $division)
                        <li class="tl-item" data-item data-id="{{ $division->id }}">
                            <button type="button" class="tl-handle" data-handle
                                    aria-label="Move {{ $division->name }}. Drag it, or press the Up and Down arrow keys.">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="6" r="1.6"/><circle cx="15" cy="6" r="1.6"/><circle cx="9" cy="12" r="1.6"/><circle cx="15" cy="12" r="1.6"/><circle cx="9" cy="18" r="1.6"/><circle cx="15" cy="18" r="1.6"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.team.divisions.update', $division) }}" class="dv-rename">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" class="pa-input" value="{{ $division->name }}" maxlength="120" required aria-label="Division name">
                                <button type="submit" class="btn btn-outline btn-sm">Save name</button>
                            </form>

                            <span class="dv-count">{{ $division->members_count }} {{ \Illuminate\Support\Str::plural('person', $division->members_count) }}</span>

                            <div class="tl-actions">
                                <form method="POST" action="{{ route('admin.team.divisions.destroy', $division) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete the division '.$division->name.'? The people in it stay, but have no division.') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="tl-status" data-order-status role="status" aria-live="polite">Order is saved when you drop a division in a new place.</p>
            @endif
        </section>

        <section class="pa-card">
            <div class="pa-card-head">
                <h3>All team members</h3>
                <p>Drag the handle on the left to change the order. It saves by itself.</p>
            </div>

            @if ($items->isEmpty())
                <p class="tl-empty">Nothing here yet. Click "Add Team member" to get started.</p>
            @else
                <ul class="tl" data-sortable data-url="{{ route('admin.team.order') }}" data-token="{{ csrf_token() }}">
                    @foreach ($items as $item)
                        <li class="tl-item" data-item data-id="{{ $item->id }}">
                            <button type="button" class="tl-handle" data-handle
                                    aria-label="Move {{ $item->name }}. Drag it, or press the Up and Down arrow keys.">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="6" r="1.6"/><circle cx="15" cy="6" r="1.6"/><circle cx="9" cy="12" r="1.6"/><circle cx="15" cy="12" r="1.6"/><circle cx="9" cy="18" r="1.6"/><circle cx="15" cy="18" r="1.6"/></svg>
                            </button>

                            <span class="tl-photo">
                                @if ($item->photo)
                                    <img src="{{ asset('storage/'.$item->photo) }}" alt="" loading="lazy">
                                @else
                                    No photo
                                @endif
                            </span>

                            <div class="tl-main">
                                <p class="tl-name">{{ $item->name }}</p>
                                <p class="tl-role">{{ $item->role }}@if ($item->division) &middot; <span class="tl-div">{{ $item->division->name }}</span>@endif @if ($item->is_featured) &middot; Home and About @endif</p>
                            </div>

                            <span class="tl-pill {{ $item->is_active ? 'is-live' : '' }}">{{ $item->is_active ? 'Shown' : 'Hidden' }}</span>

                            <div class="tl-actions">
                                <a href="{{ route('admin.team.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.team.destroy', $item) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete '.$item->name.'? This cannot be undone.') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="tl-status" data-order-status role="status" aria-live="polite">Order is saved when you drop a person in a new place.</p>
            @endif
        </section>
    </div>

    <script>
        // Hero photo: drag the photo inside the frame, use the slider to zoom. The frame has the same crop rules as the website banner.
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.querySelector('[data-hero]');
            if (!form) { return; }
            var frame = form.querySelector('[data-frame]');
            var img = form.querySelector('[data-img]');
            var empty = form.querySelector('[data-empty]');
            var inX = form.querySelector('[data-fx]');
            var inY = form.querySelector('[data-fy]');
            var inZ = form.querySelector('[data-z]');
            var slider = form.querySelector('[data-zoom]');
            var out = form.querySelector('[data-zoom-out]');
            var st = { x: parseFloat(inX.value) || 50, y: parseFloat(inY.value) || 50, z: parseFloat(inZ.value) || 1 };
            var clamp = function (v, lo, hi) { return Math.min(hi, Math.max(lo, v)); };
            var round = function (v) { return Math.round(v * 100) / 100; };

            function apply() {
                frame.style.setProperty('--fx', st.x + '%');
                frame.style.setProperty('--fy', st.y + '%');
                frame.style.setProperty('--z', st.z);
                inX.value = round(st.x);
                inY.value = round(st.y);
                inZ.value = round(st.z);
                slider.value = st.z;
                out.textContent = Math.round(st.z * 100) + '%';
            }

            // How far the picture can move: the part of the photo outside the frame, plus the extra size from zooming.
            function travel() {
                var W = frame.clientWidth, H = frame.clientHeight;
                var nw = img.naturalWidth || W, nh = img.naturalHeight || H;
                var s = Math.max(W / nw, H / nh);
                return { x: nw * s - W + (st.z - 1) * W, y: nh * s - H + (st.z - 1) * H };
            }

            var drag = null;
            frame.addEventListener('pointerdown', function (e) {
                if (img.hidden) { return; }
                drag = { px: e.clientX, py: e.clientY, x: st.x, y: st.y };
                frame.setPointerCapture(e.pointerId);
                e.preventDefault();
            });
            frame.addEventListener('pointermove', function (e) {
                if (!drag) { return; }
                var t = travel();
                if (t.x > 0.5) { st.x = clamp(drag.x - (e.clientX - drag.px) * 100 / t.x, 0, 100); }
                if (t.y > 0.5) { st.y = clamp(drag.y - (e.clientY - drag.py) * 100 / t.y, 0, 100); }
                apply();
            });
            ['pointerup', 'pointercancel'].forEach(function (name) {
                frame.addEventListener(name, function () { drag = null; });
            });
            frame.addEventListener('keydown', function (e) {
                var step = e.shiftKey ? 10 : 2;
                var moves = { ArrowLeft: [-step, 0], ArrowRight: [step, 0], ArrowUp: [0, -step], ArrowDown: [0, step] };
                if (!moves[e.key]) { return; }
                st.x = clamp(st.x + moves[e.key][0], 0, 100);
                st.y = clamp(st.y + moves[e.key][1], 0, 100);
                apply();
                e.preventDefault();
            });
            slider.addEventListener('input', function () { st.z = clamp(parseFloat(slider.value) || 1, 1, 3); apply(); });
            form.querySelector('[data-reset]').addEventListener('click', function () { st.x = 50; st.y = 50; st.z = 1; apply(); });

            // A new file shows in the frame right away, with the position reset to the middle.
            form.querySelector('[data-file]').addEventListener('change', function (e) {
                var f = e.target.files[0];
                if (!f) { return; }
                img.src = URL.createObjectURL(f);
                img.hidden = false;
                if (empty) { empty.hidden = true; }
                st.x = 50; st.y = 50; st.z = 1;
                apply();
            });
            apply();
        });
    </script>

    <script>
        // Order: drag the handle up or down (mouse or touch), or focus it and press the arrow keys. The new order is saved right away.
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-sortable]').forEach(function (list) {
            var status = list.closest('section').querySelector('[data-order-status]');
            var saveTimer = null;

            function say(text, kind) {
                if (!status) { return; }
                status.textContent = text;
                status.classList.toggle('is-ok', kind === 'ok');
                status.classList.toggle('is-error', kind === 'error');
            }

            function save() {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(function () {
                    var ids = Array.prototype.map.call(list.querySelectorAll('[data-item]'), function (li) { return Number(li.dataset.id); });
                    say('Saving the order...', '');
                    fetch(list.dataset.url, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': list.dataset.token },
                        body: JSON.stringify({ ids: ids })
                    }).then(function (r) {
                        if (!r.ok) { throw new Error('failed'); }
                        say('Order saved.', 'ok');
                    }).catch(function () {
                        say('The order could not be saved. Reload the page and try again.', 'error');
                    });
                }, 250);
            }

            var current = null;

            list.addEventListener('pointerdown', function (e) {
                var handle = e.target.closest('[data-handle]');
                if (!handle) { return; }
                current = handle.closest('[data-item]');
                current.classList.add('is-dragging');
                // The pointer is captured on the list (not on the row), because the row moves in the page while it is dragged.
                list.setPointerCapture(e.pointerId);
                e.preventDefault();
            });

            list.addEventListener('pointermove', function (e) {
                if (!current) { return; }
                var items = Array.prototype.slice.call(list.querySelectorAll('[data-item]'));
                var index = items.indexOf(current);
                var prev = items[index - 1];
                var next = items[index + 1];

                if (prev) {
                    var p = prev.getBoundingClientRect();
                    if (e.clientY < p.top + p.height / 2) { list.insertBefore(current, prev); return; }
                }
                if (next) {
                    var n = next.getBoundingClientRect();
                    if (e.clientY > n.top + n.height / 2) { list.insertBefore(next, current); }
                }
            });

            function drop() {
                if (!current) { return; }
                current.classList.remove('is-dragging');
                current = null;
                save();
            }
            list.addEventListener('pointerup', drop);
            list.addEventListener('pointercancel', drop);

            list.addEventListener('keydown', function (e) {
                var handle = e.target.closest('[data-handle]');
                if (!handle || (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) { return; }
                var item = handle.closest('[data-item]');
                var other = e.key === 'ArrowUp' ? item.previousElementSibling : item.nextElementSibling;
                if (!other) { return; }
                if (e.key === 'ArrowUp') { list.insertBefore(item, other); } else { list.insertBefore(other, item); }
                handle.focus();
                e.preventDefault();
                save();
            });
            });
        });
    </script>
</x-admin-layout>