@php
    // Baris karakter untuk repeater. Path foto hanya dipakai kalau sudah tercatat di proyek ini.
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
    .pf { width: 100%; max-width: 64rem; margin-inline: auto; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
    .pf > * + * { margin-top: 1.25rem; }
    .pf-form { display: grid; gap: 1.25rem; }
    .pf-head h2 { font-size: var(--fs-h2); }
    .pf-head p { margin-top: 0.15rem; font-size: var(--fs-small); }

    .pf-card { padding: clamp(1.25rem, 3vw, 1.75rem); background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
    .pf-card h3 { font-size: var(--fs-h4); }
    .pf-card h3 small { font-family: var(--font-body); font-size: var(--fs-small); font-weight: 400; color: var(--color-muted); }
    .pf-card .pf-hint { margin-top: 0.15rem; font-size: var(--fs-small); }
    .pf-grid { display: grid; gap: 1rem; margin-top: 1.1rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pf-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

    .pf-label { display: block; margin-bottom: 0.35rem; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
    .pf-label small { font-weight: 400; color: var(--color-muted); }
    .pf-input { width: 100%; padding: 0.65rem 0.85rem; font-family: var(--font-body); font-size: var(--fs-body); color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); transition: border-color 0.2s, box-shadow 0.2s; }
    .pf-input:focus { outline: 0; border-color: var(--color-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 18%, transparent); }
    textarea.pf-input { min-height: 9rem; resize: vertical; line-height: 1.55; }
    .pf-error { margin-top: 0.3rem; font-size: var(--fs-caption); color: var(--color-danger); }

    .pf-image { display: grid; place-items: center; aspect-ratio: 16 / 9; max-width: 28rem; overflow: hidden; color: var(--color-muted); font-size: var(--fs-small); background: color-mix(in srgb, var(--color-primary) 6%, white); border: 1px solid var(--color-border); border-radius: var(--radius-lg); }
    .pf-image img { width: 100%; height: 100%; object-fit: cover; }
    .pf-file { margin-top: 0.75rem; font-size: var(--fs-small); }
    .pf-check { display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.6rem; font-size: var(--fs-small); cursor: pointer; }
    .pf-check input { width: 1.05rem; height: 1.05rem; accent-color: var(--color-primary); }

    /* Logo judul */
    .pf-logo-stage { display: grid; place-items: center; min-height: 7.5rem; max-width: 28rem; margin-top: 1rem; padding: 1.25rem; background: color-mix(in srgb, var(--color-primary) 6%, white); border: 1px solid var(--color-border); border-radius: var(--radius-lg); transition: background 0.2s; }
    .pf-logo-stage.is-dark { background: #1b1445; border-color: #1b1445; }
    .pf-logo-img { display: block; max-width: 100%; max-height: 5rem; object-fit: contain; }
    .pf-logo-text { font-family: var(--font-heading); font-size: var(--fs-h2); font-weight: 600; line-height: 1.2; text-align: center; color: #1b1445; overflow-wrap: anywhere; }
    .pf-logo-stage.is-dark .pf-logo-text { color: #fff; }
    .pf-logo-meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; max-width: 28rem; margin-top: 0.5rem; font-size: var(--fs-caption); color: var(--color-muted); }
    .pf-toggle { display: inline-flex; padding: 0.15rem; background: color-mix(in srgb, var(--color-primary) 8%, white); border-radius: 999px; }
    .pf-toggle button { padding: 0.2rem 0.7rem; font: inherit; font-weight: 600; color: var(--color-muted); background: transparent; border: 0; border-radius: 999px; cursor: pointer; }
    .pf-toggle button.is-on { color: var(--color-primary); background: #fff; box-shadow: 0 1px 3px rgba(36, 26, 74, 0.15); }

    /* Karakter */
    .pf-chars { margin-top: 0.5rem; }
    .pf-char { display: grid; gap: 0.75rem; align-items: center; padding: 0.9rem 0; border-bottom: 1px solid var(--color-border); grid-template-columns: auto minmax(0, 1fr) minmax(0, 1fr) auto; }
    .pf-char:first-child { margin-top: 0.5rem; border-top: 1px solid var(--color-border); }
    .pf-char-photo { display: flex; align-items: center; gap: 0.75rem; }
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
        .pf-grid, .pf-grid--3 { grid-template-columns: 1fr; }
        .pf-char { grid-template-columns: 1fr; }
    }
</style>

<div class="pf">
    <div class="pf-head">
        <h2>{{ $project->exists ? 'Edit project' : 'Add project' }}</h2>
        <p>Isi versi Indonesia dulu. Versi English boleh dikosongkan.</p>
    </div>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="pf-form"
          x-data="{
              title: @js(old('title.en', $project->getTranslation('title', 'en', false))),
              logo: @js($project->logo ? asset('storage/'.$project->logo) : null),
              dropLogo: false
          }">
        @csrf
        @if ($method !== 'POST') @method($method) @endif

        {{-- ===== INFORMASI UTAMA ===== --}}
        <section class="pf-card">
            <h3>Informasi utama</h3>

            <div class="pf-grid">
                <div>
                    <label class="pf-label" for="title_id">Title <small>(Indonesian, optional)</small></label>
                    <input id="title_id" name="title[id]" type="text" class="pf-input" maxlength="150" value="{{ old('title.id', $project->getTranslation('title', 'id', false)) }}">
                    @error('title.id')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="title_en">Title <small>(English, required)</small></label>
                    <input id="title_en" name="title[en]" type="text" class="pf-input" maxlength="150" required x-model="title"
                           value="{{ old('title.en', $project->getTranslation('title', 'en', false)) }}">
                    @error('title.en')<p class="pf-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="pf-label" for="tag_id">Tag <small>(Indonesia, mis. Season 2)</small></label>
                    <input id="tag_id" name="tag[id]" type="text" class="pf-input" maxlength="80"
                           value="{{ old('tag.id', $project->getTranslation('tag', 'id', false)) }}">
                    @error('tag.id')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="tag_en">Tag <small>(English)</small></label>
                    <input id="tag_en" name="tag[en]" type="text" class="pf-input" maxlength="80"
                           value="{{ old('tag.en', $project->getTranslation('tag', 'en', false)) }}">
                    @error('tag.en')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pf-grid pf-grid--3">
                <div>
                    <label class="pf-label" for="release_year">Release year</label>
                    <input id="release_year" name="release_year" type="number" class="pf-input" min="1990" max="2100"
                           value="{{ old('release_year', $project->release_year) }}">
                    @error('release_year')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="sort_order">Order <small>(lower shows first)</small></label>
                    <input id="sort_order" name="sort_order" type="number" class="pf-input" min="0" max="9999"
                           value="{{ old('sort_order', $project->sort_order ?? 0) }}">
                    @error('sort_order')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="youtube_url">Link YouTube</label>
                    <input id="youtube_url" name="youtube_url" type="url" class="pf-input" placeholder="https://youtube.com/..."
                           value="{{ old('youtube_url', $project->youtube_url) }}">
                    @error('youtube_url')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- ===== LOGO JUDUL ===== --}}
        <section class="pf-card" x-data="{ dark: false }">
            <h3>Title logo <small>(optional)</small></h3>
            <p class="pf-hint">If a logo is set, the logo is shown as the title on the website. If empty, the text title above is shown.</p>

            <div class="pf-logo-stage" :class="{ 'is-dark': dark }">
                <template x-if="logo && !dropLogo"><img :src="logo" alt="Title logo preview" class="pf-logo-img"></template>
                <span class="pf-logo-text" x-show="!logo || dropLogo" x-text="title.trim() || 'Title not set yet'"></span>
            </div>

            <div class="pf-logo-meta">
                <span x-text="logo && !dropLogo ? 'Shown on the website: logo' : 'Shown on the website: text title'"></span>
                <span class="pf-toggle" role="group" aria-label="Preview background">
                    <button type="button" :class="{ 'is-on': !dark }" @click="dark = false">Latar terang</button>
                    <button type="button" :class="{ 'is-on': dark }" @click="dark = true">Latar gelap</button>
                </span>
            </div>

            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="pf-file"
                   @change="const f = $event.target.files[0]; if (f) { logo = URL.createObjectURL(f); dropLogo = false }">
            <p class="pf-hint">PNG transparan paling bagus, maksimal 2 MB.</p>
            @error('logo')<p class="pf-error">{{ $message }}</p>@enderror

            @if ($project->logo)
                <label class="pf-check">
                    <input type="checkbox" name="remove_logo" value="1" x-model="dropLogo">
                    Remove logo, use the text title only
                </label>
            @endif
        </section>

        {{-- ===== SINOPSIS ===== --}}
        <section class="pf-card">
            <h3>Synopsis</h3>
            <p class="pf-hint">Shown on the project detail page.</p>

            <div class="pf-grid">
                <div>
                    <label class="pf-label" for="synopsis_id">Indonesia</label>
                    <textarea id="synopsis_id" name="synopsis[id]" class="pf-input" maxlength="5000">{{ old('synopsis.id', $project->getTranslation('synopsis', 'id', false)) }}</textarea>
                    @error('synopsis.id')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="pf-label" for="synopsis_en">English</label>
                    <textarea id="synopsis_en" name="synopsis[en]" class="pf-input" maxlength="5000">{{ old('synopsis.en', $project->getTranslation('synopsis', 'en', false)) }}</textarea>
                    @error('synopsis.en')<p class="pf-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- ===== GAMBAR ===== --}}
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

        {{-- ===== KARAKTER ===== --}}
        <section class="pf-card"
                 x-data="{
                    rows: @js($charRows),
                    n: {{ count($charRows) }},
                    add() { this.rows.push({ uid: this.n++, name: '', role: '', photo: '', photo_url: '' }) },
                    remove(i) { this.rows.splice(i, 1) },
                    pick(event, row) {
                        const f = event.target.files[0];
                        if (f) { row.photo_url = URL.createObjectURL(f) }
                    },
                    clearPhoto(event, row) {
                        row.photo = '';
                        row.photo_url = '';
                        const input = event.target.closest('.pf-char').querySelector('input[type=file]');
                        if (input) { input.value = '' }
                    }
                 }">
            <h3>Characters</h3>
            <p class="pf-hint">The characters in this animation, with photos (max 2 MB each). Rows without a name are ignored.</p>

            <p class="pf-empty" x-show="rows.length === 0">No characters yet.</p>

            <div class="pf-chars">
                <template x-for="(row, i) in rows" :key="row.uid">
                    <div class="pf-char">
                        <div class="pf-char-photo">
                            <span class="pf-avatar">
                                <template x-if="row.photo_url"><img :src="row.photo_url" alt=""></template>
                                <span x-show="!row.photo_url" x-text="(row.name || '?').trim().charAt(0).toUpperCase() || '?'"></span>
                            </span>

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

        {{-- ===== PUBLIKASI + AKSI ===== --}}
        <div class="pf-bar">
            <label class="pf-check" style="margin: 0 auto 0 0">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published))>
                Tayangkan di website
            </label>

            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline btn-sm">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $project->exists ? 'Save changes' : 'Save project' }}</button>
        </div>
    </form>
</div>
