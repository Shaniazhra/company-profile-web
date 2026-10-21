<x-admin-layout>
    <style>
        /* Same page padding, header, card, and pill styles as the Site Settings page, so the admin feels like one product. */
        .pa { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .pa > * + * { margin-top: 1.5rem; }

        .pa-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-border); }
        .pa-head h2 { margin: 0; font-size: var(--fs-h3); }
        .pa-head p { margin: 0.25rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        .pa-flash { display: flex; align-items: center; gap: 0.5rem; padding: 0.8rem 1.1rem; font-size: var(--fs-small); font-weight: 500; color: #14633f; background: #e6f7ee; border: 1px solid #c4ebd5; border-radius: var(--radius); }

        .pa-card { overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .pa-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .pa-card-head h3 { margin: 0; font-size: var(--fs-h4); }
        .pa-card-head p { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }

        .pa-scroll { overflow-x: auto; }
        .pa-table { width: 100%; min-width: 46rem; border-collapse: collapse; }
        .pa-table th { padding: 0.75rem 1.5rem; font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; text-align: left; color: var(--color-muted); background: color-mix(in srgb, var(--color-primary) 4%, white); border-bottom: 1px solid var(--color-border); }
        .pa-table td { padding: 0.9rem 1.5rem; font-size: var(--fs-small); color: var(--color-muted); vertical-align: middle; }
        .pa-table tbody tr + tr td { border-top: 1px solid var(--color-border); }
        .pa-table tbody tr:hover td { background: color-mix(in srgb, var(--color-primary) 5%, white); }

        .pa-title { display: flex; align-items: center; gap: 0.9rem; min-width: 0; }
        .pa-thumb { flex: none; display: grid; place-items: center; width: 5rem; aspect-ratio: 16 / 10; overflow: hidden; color: #fff; background: var(--gradient-brand); border-radius: var(--radius); font-family: var(--font-heading); font-size: var(--fs-small); font-weight: 600; }
        .pa-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .pa-name { margin: 0; font-weight: 600; color: var(--color-text); }
        .pa-slug { margin: 0.1rem 0 0; font-size: var(--fs-caption); }

        /* The title logo is shown as it is, with no background */
        .pa-logo { display: grid; place-items: center; width: 6rem; height: 2.75rem; }
        .pa-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .pa-none { color: var(--color-muted); }

        /* Status pill: same colors as the "shown / hidden" pills in Site Settings. The whole pill is a button. */
        .pa-status { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.8rem; font: inherit; font-size: var(--fs-caption); font-weight: 600; color: #7a5a1d; background: #f7efd9; border: 0; border-radius: 999px; cursor: pointer; transition: filter 0.2s; }
        .pa-status::before { content: ""; width: 0.5rem; height: 0.5rem; border-radius: 50%; background: currentColor; }
        .pa-status.is-live { color: #1d7a55; background: #e2f4ec; }
        .pa-status:hover { filter: brightness(0.96); }
        .pa-status:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }

        .pa-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .pa-actions form { margin: 0; }

        .pa-empty { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 3rem 1.25rem; text-align: center; color: var(--color-muted); }
        .pa-empty p { margin: 0; }
        /* Hero photo of the public Projects page */
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
        .pa-pager { padding: 0.9rem 1.5rem; border-top: 1px solid var(--color-border); }

        @media (max-width: 760px) {
            .pa-card-head, .pa-table th, .pa-table td, .pa-pager { padding-inline: 1rem; }
            .pa-hide-sm { display: none; }
            .pa-table { min-width: 34rem; }
            .pa-hero { grid-template-columns: 1fr; padding-inline: 1rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            .pa-status { transition: none; }
        }
    </style>

    <div class="pa">
        <div class="pa-head">
            <div>
                <h2>Projects</h2>
                <p>The published project with the lowest order is featured on the home page.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                <x-admin.icon name="plus" />
                Add project
            </a>
        </div>

        @if (session('status'))
            <div class="pa-flash" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ session('status') }}</div>
        @endif

        {{-- Hero photo of the public Projects page --}}
        @php $heroPath = \App\Models\SiteSetting::first()?->projects_image; @endphp
        <section class="pa-card">
            <div class="pa-card-head">
                <h3>Page hero photo</h3>
                <p>The banner photo at the very top of the Projects page on the website.</p>
            </div>
            @php
                $hx = (float) (\App\Models\SiteSetting::first()?->projects_image_x ?? 50);
                $hy = (float) (\App\Models\SiteSetting::first()?->projects_image_y ?? 50);
                $hz = (float) (\App\Models\SiteSetting::first()?->projects_image_zoom ?? 1);
            @endphp
            <form method="POST" action="{{ route('admin.projects.hero.update') }}" enctype="multipart/form-data" class="pa-hero" data-hero>
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
                    <input type="file" name="projects_image" accept="image/png,image/jpeg,image/webp" class="pa-file" data-file aria-label="Choose the hero photo">
                    <div class="pa-hero-buttons">
                        <button type="submit" class="btn btn-primary btn-sm">Save hero photo</button>
                        @if ($heroPath)
                            <button type="submit" form="hero-remove" class="btn btn-outline btn-sm" onclick="return confirm('Remove the hero photo?');">Remove photo</button>
                        @endif
                    </div>
                    @error('projects_image')<p class="pa-error" role="alert">{{ $message }}</p>@enderror
                    <p class="pa-hint">Wide landscape photo, at least 2000 px wide. PNG, JPG, or WebP, max 5 MB. The website darkens it so the white title stays readable. Empty shows the plain dark banner.</p>
                </div>
            </form>
            <form id="hero-remove" method="POST" action="{{ route('admin.projects.hero.destroy') }}" hidden>@csrf @method('DELETE')</form>
        </section>

        <section class="pa-card">
            @if ($projects->isEmpty())
                <div class="pa-empty">
                    <p>No projects yet.</p>
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-outline btn-sm">
                        <x-admin.icon name="plus" />
                        Add the first project
                    </a>
                </div>
            @else
                <div class="pa-card-head">
                    <h3>All projects</h3>
                    <p>Click a status to switch between Published and Draft.</p>
                </div>

                {{-- Every title field points to this form with form="titles-form", so one button saves all titles. --}}
                <form id="titles-form" method="POST" action="{{ route('admin.projects.titles') }}" hidden>@csrf @method('PUT')</form>

                <div class="pa-scroll">
                    <table class="pa-table">
                        <thead>
                            <tr>
                                <th>Project title</th>
                                <th>Logo</th>
                                <th class="pa-hide-sm">Tag</th>
                                <th class="pa-hide-sm">Year</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                @php $name = $project->title ?: $project->name; @endphp
                                <tr>
                                    <td>
                                        <div class="pa-title">
                                            <span class="pa-thumb">
                                                @if ($project->image)
                                                    <img src="{{ asset('storage/'.$project->image) }}" alt="" loading="lazy">
                                                @else
                                                    {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
                                                @endif
                                            </span>
                                            <div>
                                                <input type="text" name="titles[{{ $project->id }}]" form="titles-form" class="pa-input" maxlength="120" required
                                                       value="{{ old('titles.'.$project->id, $name) }}" aria-label="Title of {{ $name }}">
                                                @error('titles.'.$project->id)<p class="pa-error" role="alert">{{ $message }}</p>@enderror
                                                @if ($project->slug)<p class="pa-slug">/{{ $project->slug }}</p>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($project->logo)
                                            <span class="pa-logo"><img src="{{ asset('storage/'.$project->logo) }}" alt="{{ $name }} logo" loading="lazy"></span>
                                        @else
                                            <span class="pa-none">-</span>
                                        @endif
                                    </td>
                                    <td class="pa-hide-sm">{{ $project->getTranslation('tag', 'en', false) ?: '-' }}</td>
                                    <td class="pa-hide-sm">{{ $project->release_year ?: '-' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.projects.publish', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="pa-status {{ $project->is_published ? 'is-live' : '' }}"
                                                    title="Click to change the status">
                                                {{ $project->is_published ? 'Published' : 'Draft' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="pa-actions">
                                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline btn-sm">
                                                <x-admin.icon name="edit" />
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete project "'.$name.'"? This cannot be undone.') }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pa-savebar">
                    <p>Type a title in the Project column. It is shown on the Projects pages. The home page keeps showing the title logo.</p>
                    <button type="submit" form="titles-form" class="btn btn-primary btn-sm">Save titles</button>
                </div>

                @if ($projects->hasPages())
                    <div class="pa-pager">{{ $projects->links() }}</div>
                @endif
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
</x-admin-layout>