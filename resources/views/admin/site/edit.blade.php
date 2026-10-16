<x-admin-layout>

    <style>
        /* Jarak samping sama dengan bar atas supaya sejajar dengan hamburger dan avatar admin */
        .site-page { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .site-page > * + * { margin-top: 1.75rem; }
        .site-intro { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }

        .site-group { margin: 0 0 0.75rem; font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--color-primary); }

        .site-grid { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 1100px) { .site-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

        /* Kartu */
        .site-card {
            scroll-margin-top: 5.5rem;
            overflow: hidden;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45);
        }
        .site-card-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .site-card-head h3 { margin: 0; font-size: var(--fs-h4); }
        .site-card-head p { margin-top: 0.15rem; font-size: var(--fs-small); color: var(--color-muted); }
        .site-card-body { padding: 1.5rem; }
        .site-card-foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 0.85rem 1.5rem;
            border-top: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-primary) 4%, white);
        }
        .site-card-foot:empty { display: none; }
        .site-note { display: inline-flex; align-items: center; gap: 0.4rem; margin: 0 auto 0 0; font-size: var(--fs-small); font-weight: 500; color: #1d7a55; }

        /* Unggah gambar */
        .img-up-stage {
            position: relative;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: #eeeaf7;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
        }
        .img-up-stage.is-dark { background: #1c1640; }
        .is-logo .img-up-stage { min-height: 7.5rem; padding: 1rem; }
        .is-logo .img-up-img { width: auto; max-width: 100%; max-height: 5.5rem; object-fit: contain; }
        .is-hero .img-up-stage { aspect-ratio: 16 / 6; }
        .is-hero .img-up-img { width: 100%; height: 100%; object-fit: cover; }
        .img-up-empty { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }
        .is-dark .img-up-empty { color: rgba(255, 255, 255, 0.65); }
        .img-up-badge { position: absolute; top: 0.6rem; left: 0.6rem; padding: 0.15rem 0.65rem; font-size: var(--fs-caption); font-weight: 600; color: #fff; background: var(--color-primary); border-radius: 999px; }

        .img-up-controls { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-top: 1rem; }
        .img-up-file { max-width: 100%; font-size: var(--fs-small); color: var(--color-muted); }
        .img-up-file::file-selector-button {
            margin-right: 0.75rem;
            padding: 0.5rem 1rem;
            font: inherit;
            font-weight: 500;
            color: var(--color-primary);
            background: #fff;
            border: var(--border-width) solid var(--color-primary);
            border-radius: var(--radius);
            cursor: pointer;
        }
        .img-up-file::file-selector-button:hover { color: #fff; background: var(--color-primary); }
        .img-up-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .img-up-filename { max-width: 14rem; overflow: hidden; font-size: var(--fs-small); color: var(--color-muted); text-overflow: ellipsis; white-space: nowrap; }
        .img-up-hint { margin: 0.75rem 0 0; font-size: var(--fs-caption); color: var(--color-muted); }
        .img-up-error { margin: 0.5rem 0 0; font-size: var(--fs-small); color: var(--color-danger); }

        @media (max-width: 640px) {
            .site-card-head, .site-card-body, .site-card-foot { padding-inline: 1rem; }
            .site-card-foot .btn { width: 100%; }
        }
    </style>

    @php
        // Pesan sukses hanya tampil di kartu yang barusan disimpan.
        $flash = fn (string $key) => session('section') === $key ? session('status') : null;

        $texts = [
            ['field' => 'tagline',          'title' => 'Tagline',          'desc' => 'Judul besar di bagian utama (hero) halaman depan.', 'rows' => 1],
            ['field' => 'heart_statement',  'title' => 'Heart Statement',  'desc' => 'Kalimat pendukung di bawah judul hero.',            'rows' => 4],
            ['field' => 'impact_statement', 'title' => 'Impact Statement', 'desc' => 'Impact statement shown on the home page and the About page.', 'rows' => 4],
            ['field' => 'about_text',       'title' => 'About Text', 'desc' => 'Studio description on the About page.',        'rows' => 6],
        ];
    @endphp

    <div class="site-page">

        <p class="site-intro">Each section is saved on its own, so you can change one section without filling in the others.</p>

        {{-- ===== Identitas ===== --}}
        <div>
            <p class="site-group">Identitas</p>

            <section id="sec-name" class="site-card">
                <form method="POST" action="{{ route('admin.site.name') }}">
                    @csrf
                    @method('PUT')

                    <div class="site-card-head">
                        <h3>Nama Situs</h3>
                        <p>Tampil di judul tab browser, navbar, dan footer.</p>
                    </div>

                    <div class="site-card-body">
                        <x-input-label for="site_name" value="Nama Situs" />
                        <x-text-input id="site_name" name="site_name" type="text" class="mt-1 block w-full"
                                      :value="old('site_name', $site->site_name)" required />
                        <x-input-error :messages="$errors->get('site_name')" class="mt-1" />
                    </div>

                    <div class="site-card-foot">
                        @if ($msg = $flash('name'))
                            <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                        @endif
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </section>
        </div>

        {{-- ===== Gambar ===== --}}
        <div>
            <p class="site-group">Gambar</p>

            <div class="site-grid site-grid--2">
                <x-admin.image-upload
                    name="logo" kind="logo" title="Logo"
                    description="Shown in the navbar and footer. If empty, the site name is shown."
                    :path="$site->logo"
                    :upload-route="route('admin.site.image.upload', 'logo')"
                    :delete-route="route('admin.site.image.destroy', 'logo')"
                    :max-mb="2" :dark="true"
                    help="PNG transparan disarankan, tinggi sekitar 80 px. Maksimal 2 MB."
                    :message="$flash('logo')" />

                <x-admin.image-upload
                    name="hero_image" kind="hero" title="Gambar Latar Hero"
                    description="Gambar latar bagian utama halaman depan. Kalau kosong, hero memakai gradasi."
                    :path="$site->hero_image"
                    :upload-route="route('admin.site.image.upload', 'hero_image')"
                    :delete-route="route('admin.site.image.destroy', 'hero_image')"
                    :max-mb="5" :dark="true"
                    help="Landscape photo, at least 1600 px wide, max 5 MB. Keep the left side uncluttered so the text stays readable."
                    :message="$flash('hero_image')" />
            </div>
        </div>

        {{-- ===== Teks ===== --}}
        <div>
            <p class="site-group">Teks website</p>

            <div class="site-grid site-grid--2">
                @foreach ($texts as $t)
                    <section id="sec-{{ $t['field'] }}" class="site-card">
                        <form method="POST" action="{{ route('admin.site.text', $t['field']) }}">
                            @csrf
                            @method('PUT')

                            <div class="site-card-head">
                                <h3>{{ $t['title'] }}</h3>
                                <p>{{ $t['desc'] }}</p>
                            </div>

                            <div class="site-card-body">
                                <x-admin.translatable :name="$t['field']" label="Isi per bahasa" :model="$site" :rows="$t['rows']" />
                            </div>

                            <div class="site-card-foot">
                                @if ($msg = $flash($t['field']))
                                    <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            </div>
                        </form>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Kalau ada pesan error validasi, langsung gulir ke error pertama.
        document.addEventListener('DOMContentLoaded', function () {
            var firstError = document.querySelector('.text-red-600');
            if (firstError) { firstError.scrollIntoView({ block: 'center' }); }
        });
    </script>
</x-admin-layout>