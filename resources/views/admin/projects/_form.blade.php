@php
    // Character rows for the repeater. A photo path is only kept if it is already saved on this project.
    $existingPhotos = collect($project->characters ?? [])->pluck('photo')->filter()->all();
    $charRows = collect(old('characters', $project->characters ?? []))
        ->values()
        ->map(function ($c, $n) use ($existingPhotos) {
            $photo = in_array($c['photo'] ?? null, $existingPhotos, true) ? $c['photo'] : '';

            return [
                'uid'       => $n,
                'name'      => $c['name'] ?? '',
                'role'      => $c['role'] ?? '',
                'photo'     => $photo,
                'photo_url' => $photo ? asset('storage/'.$photo) : '',
            ];
        })
        ->all();
@endphp

<style>
    .pf { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
    .pf > * + * { margin-top: 1.25rem; }
    .pf-form { display: grid; gap: 1.25rem; }
    .pf-head h2 { font-size: var(--fs-h2); }
    .pf-head p { margin-top: 0.15rem; font-size: var(--fs-small); }

    .pf-card { padding: clamp(1.25rem, 3vw, 1.75rem); background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
    .pf-card h3 { font-size: var(--fs-h4); }
    .pf-card h3 small { font-family: var(--font-body); font-size: var(--fs-small); font-weight: 400; color: var(--color-muted); }
    .pf-card .pf-hint { margin-top: 0.15rem; font-size: var(--fs-small); }
    .pf-grid { display: grid; gap: 1rem; margin-top: 1.1rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pf-grid--1 { grid-template-columns: 1fr; }

    .pf-label { display: block; margin-bottom: 0.35rem; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
    .pf-label small { font-weight: 400; color: var(--color-muted); }
    .pf-input { width: 100%; padding: 0.65rem 0.85rem; font-family: var(--font-body); font-size: var(--fs-body); color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); transition: border-color 0.2s, box-shadow 0.2s; }
    .pf-input:focus { outline: 0; border-color: var(--color-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 18%, transparent); }
    textarea.pf-input { min-height: 9rem; resize: vertical; line-height: 1.55; }
    .pf-alert { padding: 1rem 1.25rem; font-size: var(--fs-small); background: color-mix(in srgb, var(--color-danger) 8%, white); border: 1px solid var(--color-danger); border-radius: var(--radius-lg); }
    .pf-alert strong { display: block; margin-bottom: 0.25rem; color: var(--color-danger); }
    .pf-alert ul { margin: 0; padding-left: 1.1rem; }
    .pf-error { margin-top: 0.3rem; font-size: var(--fs-caption); color: var(--color-danger); }

    .pf-image { display: grid; place-items: center; aspect-ratio: 16 / 9; max-width: 28rem; overflow: hidden; color: var(--color-muted); font-size: var(--fs-small); background: color-mix(in srgb, var(--color-primary) 6%, white); border: 1px solid var(--color-border); border-radius: var(--radius-lg); }
    .pf-image img { width: 100%; height: 100%; object-fit: cover; }
    .pf-file { margin-top: 0.75rem; font-size: var(--fs-small); }
    .pf-check { display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.6rem; font-size: var(--fs-small); cursor: pointer; }
    .pf-check input { width: 1.05rem; height: 1.05rem; accent-color: var(--color-primary); }

    /* Title logo */
    .pf-logo-stage { display: grid; place-items: center; min-height: 7.5rem; max-width: 28rem; margin-top: 1rem; padding: 1.25rem; background: color-mix(in srgb, var(--color-primary) 6%, white); border: 1px solid var(--color-border); border-radius: var(--radius-lg); transition: background 0.2s; }
    .pf-logo-stage.is-dark { background: #1b1445; border-color: #1b1445; }
    .pf-logo-img { display: block; max-width: 100%; max-height: 5rem; object-fit: contain; }
    .pf-logo-empty { font-size: var(--fs-small); color: var(--color-muted); }
    .pf-logo-stage.is-dark .pf-logo-empty { color: #cdc7e3; }
    .pf-logo-meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; max-width: 28rem; margin-top: 0.5rem; font-size: var(--fs-caption); color: var(--color-muted); }
    .pf-toggle { display: inline-flex; padding: 0.15rem; background: color-mix(in srgb, var(--color-primary) 8%, white); border-radius: 999px; }
    .pf-toggle button { padding: 0.2rem 0.7rem; font: inherit; font-weight: 600; color: var(--color-muted); background: transparent; border: 0; border-radius: 999px; cursor: pointer; }
    .pf-toggle button.is-on { color: var(--color-primary); background: #fff; box-shadow: 0 1px 3px rgba(36, 26, 74, 0.15); }

    /* Characters */
    .pf-chars { margin-top: 0.5rem; }
    .pf-char { display: grid; gap: 0.75rem; align-items: center; padding: 0.9rem 0; border-bottom: 1px solid var(--color-border); grid-template-columns: auto minmax(0, 1fr) minmax(0, 1fr) auto; }
    .pf-char:first-child { margin-top: 0.5rem; border-top: 1px solid var(--color-border); }
    .pf-char-photo { display: flex; align-items: center; gap: 0.9rem; }
    .pf-crop { display: grid; justify-items: center; gap: 0.5rem; }
    .pf-crop-canvas { display: block; width: 6rem; height: 6rem; background: color-mix(in srgb, var(--color-primary) 8%, white); border: 1px solid var(--color-border); border-radius: 50%; cursor: grab; touch-action: none; }
    .pf-crop-canvas:active { cursor: grabbing; }
    .pf-crop-canvas:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
    .pf-zoom { width: 6rem; accent-color: var(--color-primary); }
    .pf-avatar { flex: none; display: grid; place-items: center; width: 3.5rem; height: 3.5rem; overflow: hidden; color: #fff; background: var(--gradient-brand); border-radius: 50%; font-family: var(--font-heading); font-size: var(--fs-h4); font-weight: 600; }
    .pf-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .pf-photo-actions { display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; }
    .pf-photo-btn { position: relative; display: inline-flex; padding: 0.3rem 0.8rem; font-size: var(--fs-caption); font-weight: 600; color: var(--color-primary); background: color-mix(in srgb, var(--color-primary) 10%, white); border-radius: 999px; cursor: pointer; transition: background 0.2s; }
    .pf-photo-btn:hover, .pf-photo-btn:focus-within { background: color-mix(in srgb, var(--color-primary) 18%, white); }
    .pf-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; }
    .pf-link { padding: 0; font: inherit; font-size: var(--fs-caption); color: var(--color-muted); background: none; border: 0; cursor: pointer; }
    .pf-link:hover { color: var(--color-danger); text-decoration: underline; }
    .pf-empty { margin-top: 0.9rem; font-size: var(--fs-small); }
    .pf-add { margin-top: 1rem; }

    .pf-bar { position: sticky; bottom: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 0.6rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(6px); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 -10px 30px -22px rgba(36, 26, 74, 0.5); }

    @media (max-width: 720px) {
        .pf-grid { grid-template-columns: 1fr; }
        .pf-char { grid-template-columns: 1fr; }
    }
</style>

<script>
    /* Photo framing for characters. The photo is drawn on a square canvas; dragging (or the arrow keys) moves it,
       the slider zooms it. The framed square is written back into the file input, so what you see is what is uploaded. */
    window.pfCrop = window.pfCrop || (function () {
        const SIZE = 480;
        const imgs = new Map();
        const views = new Map();
        const types = new Map();
        const names = new Map();
        const clamp = (n) => Math.min(1, Math.max(0, n));

        function layout(img, v) {
            const scale = Math.max(SIZE / img.naturalWidth, SIZE / img.naturalHeight) * v.zoom;
            const w = img.naturalWidth * scale;
            const h = img.naturalHeight * scale;
            return { w, h, dx: (SIZE - w) * v.x, dy: (SIZE - h) * v.y };
        }

        function draw(root, uid) {
            const canvas = root.querySelector('canvas');
            const img = imgs.get(uid);
            const v = views.get(uid);
            if (!canvas || !img || !v) { return; }
            const l = layout(img, v);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, SIZE, SIZE);
            ctx.drawImage(img, l.dx, l.dy, l.w, l.h);
        }

        function commit(root, uid) {
            const canvas = root.querySelector('canvas');
            const input = root.querySelector('input[type=file]');
            if (!canvas || !input || !imgs.has(uid) || !names.has(uid)) { return; }
            const type = types.get(uid) === 'image/jpeg' ? 'image/jpeg' : 'image/png';
            try {
                canvas.toBlob((blob) => {
                    if (!blob) { return; }
                    const dt = new DataTransfer();
                    dt.items.add(new File([blob], names.get(uid), { type }));
                    input.files = dt.files;
                }, type, 0.92);
            } catch (e) { /* A photo served from another origin cannot be re-exported; the saved photo stays as it is. */ }
        }

        return {
            // source: a File (a new photo, which is exported right away) or a URL (a saved photo, only exported once moved)
            load(root, uid, source) {
                const isFile = typeof source !== 'string';
                const img = new Image();
                img.onload = () => {
                    imgs.set(uid, img);
                    views.set(uid, { zoom: 1, x: 0.5, y: 0.5 });
                    if (isFile) { types.set(uid, source.type); names.set(uid, source.name); } else { types.set(uid, 'image/png'); names.set(uid, 'photo.png'); }
                    const zoom = root.querySelector('.pf-zoom');
                    if (zoom) { zoom.value = 1; }
                    draw(root, uid);
                    if (isFile) { commit(root, uid); }
                };
                img.src = isFile ? URL.createObjectURL(source) : source;
            },
            forget(uid) { imgs.delete(uid); views.delete(uid); types.delete(uid); names.delete(uid); },
            drag(event, root, uid) {
                const canvas = event.currentTarget;
                const img = imgs.get(uid);
                const v = views.get(uid);
                if (!img || !v) { return; }
                const k = SIZE / canvas.getBoundingClientRect().width;
                const start = layout(img, v);
                const from = { x: event.clientX, y: event.clientY };
                canvas.setPointerCapture(event.pointerId);
                const move = (e) => {
                    const l = layout(img, v);
                    if (SIZE - l.w < 0) { v.x = clamp((start.dx + (e.clientX - from.x) * k) / (SIZE - l.w)); }
                    if (SIZE - l.h < 0) { v.y = clamp((start.dy + (e.clientY - from.y) * k) / (SIZE - l.h)); }
                    draw(root, uid);
                };
                const end = () => {
                    canvas.removeEventListener('pointermove', move);
                    canvas.removeEventListener('pointerup', end);
                    canvas.removeEventListener('pointercancel', end);
                    commit(root, uid);
                };
                canvas.addEventListener('pointermove', move);
                canvas.addEventListener('pointerup', end);
                canvas.addEventListener('pointercancel', end);
            },
            key(event, root, uid) {
                const v = views.get(uid);
                const step = { ArrowLeft: [0.05, 0], ArrowRight: [-0.05, 0], ArrowUp: [0, 0.05], ArrowDown: [0, -0.05] }[event.key];
                if (!v || !step) { return; }
                event.preventDefault();
                v.x = clamp(v.x + step[0]);
                v.y = clamp(v.y + step[1]);
                draw(root, uid);
                commit(root, uid);
            },
            zoom(event, root, uid) {
                const v = views.get(uid);
                if (!v) { return; }
                v.zoom = Number(event.target.value);
                draw(root, uid);
            },
            commit,
        };
    })();
</script>

<div class="pf">
    <div class="pf-head">
        <h2>{{ $project->exists ? 'Edit project' : 'Add project' }}</h2>
    </div>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="pf-form"
          x-data="{ logo: @js($project->logo ? asset('storage/'.$project->logo) : null) }">
        @csrf
        @if ($method !== 'POST') @method($method) @endif

        @if ($errors->any())
            <div class="pf-alert" role="alert">
                <strong>The project could not be saved.</strong>
                <ul>
                    @foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- ===== MAIN INFORMATION ===== --}}
        <section class="pf-card">
            <h3>Main information</h3>

            <div class="pf-grid">
                <div style="grid-column: 1 / -1;">
                    <label class="pf-label" for="title">Project title <small>(text shown on the Projects pages. The home page shows the title logo.)</small></label>
                    <input id="title" name="title" type="text" class="pf-input" maxlength="120" required
                           value="{{ old('title', $project->title ?: ($project->exists ? $project->name : '')) }}">
                    @error('title')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="tag_en">Tag <small>(e.g. Season 2)</small></label>
                    <input id="tag_en" name="tag[en]" type="text" class="pf-input" maxlength="80"
                           value="{{ old('tag.en', $project->getTranslation('tag', 'en', false)) }}">
                    @error('tag.en')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="release_year">Release year</label>
                    <input id="release_year" name="release_year" type="number" class="pf-input" min="1990" max="2100"
                           value="{{ old('release_year', $project->release_year) }}">
                    @error('release_year')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="youtube_url">YouTube link</label>
                    <input id="youtube_url" name="youtube_url" type="url" class="pf-input" placeholder="https://youtube.com/..."
                           value="{{ old('youtube_url', $project->youtube_url) }}">
                    @error('youtube_url')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- ===== TITLE LOGO ===== --}}
        <section class="pf-card" x-data="{ dark: false }">
            <h3>Title logo</h3>
            <p class="pf-hint">The logo is shown as the project title on the website.</p>

            <div class="pf-logo-stage" :class="{ 'is-dark': dark }">
                <template x-if="logo"><img :src="logo" alt="Title logo preview" class="pf-logo-img"></template>
                <span class="pf-logo-empty" x-show="!logo">No logo yet</span>
            </div>

            <div class="pf-logo-meta">
                <span>Preview</span>
                <span class="pf-toggle" role="group" aria-label="Preview background">
                    <button type="button" :class="{ 'is-on': !dark }" @click="dark = false">Light background</button>
                    <button type="button" :class="{ 'is-on': dark }" @click="dark = true">Dark background</button>
                </span>
            </div>

            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="pf-file" @if (! $project->logo) required @endif
                   @change="const f = $event.target.files[0]; if (f) { logo = URL.createObjectURL(f) }">
            <p class="pf-hint">A transparent PNG works best, max 2 MB.</p>
            @error('logo')<p class="pf-error">{{ $message }}</p>@enderror
        </section>

        {{-- ===== SYNOPSIS ===== --}}
        <section class="pf-card">
            <h3>Synopsis</h3>
            <p class="pf-hint">Shown on the project detail page.</p>

            <div class="pf-grid pf-grid--1">
                <div>
                    <label class="pf-label pf-sr" for="synopsis_en">Synopsis</label>
                    <textarea id="synopsis_en" name="synopsis[en]" class="pf-input" maxlength="5000">{{ old('synopsis.en', $project->getTranslation('synopsis', 'en', false)) }}</textarea>
                    @error('synopsis.en')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- ===== IMAGE ===== --}}
        <section class="pf-card" x-data="{ preview: @js($project->image ? asset('storage/'.$project->image) : null), removed: false }">
            <h3>Project image</h3>
            <p class="pf-hint">PNG, JPG, or WebP, max 5 MB. A 16:9 ratio is recommended.</p>

            <div class="pf-image" style="margin-top: 1rem">
                <template x-if="preview && !removed"><img :src="preview" alt="Project image preview"></template>
                <span x-show="!preview || removed">No image yet</span>
            </div>

            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="pf-file"
                   @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); removed = false }">
            @error('image')<p class="pf-error">{{ $message }}</p>@enderror

            @if ($project->image)
                <label class="pf-check">
                    <input type="checkbox" name="remove_image" value="1" x-model="removed">
                    Remove current image
                </label>
            @endif
        </section>

        {{-- ===== CHARACTERS ===== --}}
        <section class="pf-card"
                 x-data="{
                    rows: @js($charRows),
                    n: {{ count($charRows) }},
                    add() { this.rows.push({ uid: this.n++, name: '', role: '', photo: '', photo_url: '' }) },
                    remove(i) { this.rows.splice(i, 1) },
                    pick(event, row) {
                        const f = event.target.files[0];
                        if (!f) { return }
                        row.photo_url = 'new';
                        pfCrop.load(event.target.closest('.pf-char'), row.uid, f);
                    },
                    clearPhoto(event, row) {
                        row.photo = '';
                        row.photo_url = '';
                        pfCrop.forget(row.uid);
                        const input = event.target.closest('.pf-char').querySelector('input[type=file]');
                        if (input) { input.value = '' }
                    }
                 }">
            <h3>Characters</h3>
            <p class="pf-hint">The characters in this animation, with photos (max 2 MB each). Drag a photo to reposition it and use the slider to zoom. Rows without a name are ignored.</p>

            <p class="pf-empty" x-show="rows.length === 0">No characters yet.</p>

            <div class="pf-chars">
                <template x-for="(row, i) in rows" :key="row.uid">
                    <div class="pf-char" x-init="$nextTick(() => { if (row.photo_url) { pfCrop.load($el, row.uid, row.photo_url) } })">
                        <div class="pf-char-photo">
                            <div class="pf-crop" x-show="row.photo_url">
                                <canvas class="pf-crop-canvas" width="480" height="480" tabindex="0"
                                        aria-label="Character photo. Drag to reposition, or use the arrow keys."
                                        @pointerdown.prevent="pfCrop.drag($event, $el.closest('.pf-char'), row.uid)"
                                        @keydown="pfCrop.key($event, $el.closest('.pf-char'), row.uid)"></canvas>
                                <input type="range" class="pf-zoom" min="1" max="3" step="0.05" value="1" aria-label="Zoom"
                                       @input="pfCrop.zoom($event, $el.closest('.pf-char'), row.uid)"
                                       @change="pfCrop.commit($el.closest('.pf-char'), row.uid)">
                            </div>
                            <span class="pf-avatar" x-show="!row.photo_url" x-text="(row.name || '?').trim().charAt(0).toUpperCase() || '?'"></span>

                            <div class="pf-photo-actions">
                                <label class="pf-photo-btn">
                                    <span x-text="row.photo_url ? 'Change photo' : 'Choose photo'"></span>
                                    <input type="file" class="pf-sr" accept="image/png,image/jpeg,image/webp"
                                           :name="`characters[${i}][photo_file]`" @change="pick($event, row)">
                                </label>
                                <button type="button" class="pf-link" x-show="row.photo_url" @click="clearPhoto($event, row)">Remove photo</button>
                            </div>
                        </div>

                        <input type="hidden" :name="`characters[${i}][photo]`" :value="row.photo">

                        <input type="text" class="pf-input" placeholder="Character name" maxlength="80"
                               :name="`characters[${i}][name]`" x-model="row.name" aria-label="Character name">
                        <input type="text" class="pf-input" placeholder="Role (e.g. Main character)" maxlength="120"
                               :name="`characters[${i}][role]`" x-model="row.role" aria-label="Character role">

                        <button type="button" class="btn btn-danger btn-sm" @click="remove(i)">Delete</button>
                    </div>
                </template>
            </div>

            @error('characters')<p class="pf-error">{{ $message }}</p>@enderror
            @error('characters.*.name')<p class="pf-error">{{ $message }}</p>@enderror
            @error('characters.*.role')<p class="pf-error">{{ $message }}</p>@enderror
            @error('characters.*.photo_file')<p class="pf-error">{{ $message }}</p>@enderror

            <button type="button" class="btn btn-outline btn-sm pf-add" @click="add()">
                <x-admin.icon name="plus" />
                Add character
            </button>
        </section>

        {{-- ===== PUBLISH + ACTIONS ===== --}}
        <div class="pf-bar">
            <label class="pf-check" style="margin: 0 auto 0 0">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published))>
                Show on the website
            </label>

            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline btn-sm">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $project->exists ? 'Save changes' : 'Save project' }}</button>
        </div>
    </form>
</div>