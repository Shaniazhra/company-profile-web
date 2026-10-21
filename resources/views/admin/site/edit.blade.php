<x-admin-layout>

    <style>
        /* Side padding matches the top bar so the page lines up with the hamburger and the admin avatar */
        .site-page { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .site-page > * + * { margin-top: 2.5rem; }
        .site-intro { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }

        /* Jump links */
        .site-jump { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-top: 1rem; }
        .site-jump-label { margin-right: 0.25rem; font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-muted); }
        .site-jump a {
            padding: 0.4rem 0.9rem;
            font-size: var(--fs-small);
            font-weight: 500;
            color: var(--color-primary);
            text-decoration: none;
            background: #fff;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            transition: background-color 0.2s, color 0.2s;
        }
        .site-jump a:hover { color: #fff; background: var(--color-primary); }
        .site-jump + .site-jump { margin-top: 0.6rem; }

        /* Group heading */
        .site-group { scroll-margin-top: 5.5rem; padding-bottom: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); }
        .site-group h2 { margin: 0; font-size: var(--fs-h3); }
        .site-group p { margin: 0.25rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }
        .site-sub { margin: 1.75rem 0 0.75rem; font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--color-primary); }
        .site-group + .site-sub { margin-top: 0; }

        .site-grid { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 1100px) { .site-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

        /* A numbered step = one section of the About page, in the same order as the website */
        .site-step { scroll-margin-top: 5.5rem; }
        .site-step { margin-top: 2.75rem; padding-top: 2rem; border-top: 1px dashed var(--color-border); }
        .site-group + .site-step { margin-top: 0; padding-top: 0; border-top: 0; }
        .site-step-head { display: flex; align-items: flex-start; gap: 0.85rem; margin-bottom: 1.25rem; }
        .site-step-num { flex: none; display: grid; place-items: center; width: 2rem; height: 2rem; font-size: var(--fs-small); font-weight: 700; color: #fff; background: var(--color-primary); border-radius: 50%; }
        .site-step-head h3 { margin: 0; font-size: var(--fs-h4); }
        .site-step-head p { margin: 0.15rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        /* Text on the left, photos on the right */
        .site-cols { display: grid; gap: 1.25rem; align-items: start; }
        .site-col { display: grid; gap: 1.25rem; min-width: 0; align-content: start; }
        @media (min-width: 1100px) { .site-cols { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }


        /* Card */
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

        /* Image upload */
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
        .img-up-empty { margin: 0; padding: 0 1rem; font-size: var(--fs-small); text-align: center; color: var(--color-muted); }
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

        /* Text field + Save all bar */
        .site-textarea { display: block; width: 100%; padding: 0.65rem 0.85rem; font: inherit; font-size: var(--fs-small); line-height: 1.55; color: var(--color-text); background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius); resize: vertical; }
        .site-textarea:focus { outline: 3px solid var(--focus, var(--color-primary)); outline-offset: 1px; border-color: var(--color-primary); }
        .site-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding: 0.85rem 1.25rem; background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 -10px 24px -18px rgba(36, 26, 74, 0.45); }
        .site-savebar .site-note { margin-right: auto; }
        .site-savebar-hint { margin: 0 auto 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        @media (max-width: 640px) {
            .site-card-head, .site-card-body, .site-card-foot { padding-inline: 1rem; }
            .site-card-foot .btn { width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            .site-jump a { transition: none; }
        }
    </style>

    @php
        // The success message only shows on the card that was just saved.
        $flash = fn (string $key) => session('section') === $key ? session('status') : null;

        // ---- Home page texts ----
        $homeTexts = [
            ['field' => 'tagline',         'title' => 'Tagline',         'desc' => 'Large headline in the hero section of the home page.', 'rows' => 1],
            ['field' => 'heart_statement', 'title' => 'Heart Statement', 'desc' => 'Supporting sentence under the hero headline.',           'rows' => 4],
        ];

        // ---- About page, in the same order as the website ----
        // 1. Hero banner (photo only)
        $photoHero = ['name' => 'about_image', 'title' => 'Hero Photo', 'w' => 5, 'h' => 1, 'max' => '60rem', 'long' => 3000,
            'desc' => 'Background of the dark banner at the top of the About page.',
            'help' => ' The frame is the desktop banner shape, and the website darkens the photo so the white title stays readable. On phones the banner is taller and shows the middle of the photo, so keep the subject near the center. Use a wide original, at least 2000 px.'];

        // 2. Who We Are
        $textsWho = [
            ['field' => 'about_text',  'title' => 'Intro Text',   'desc' => 'Paragraph of the first section under the banner.',                       'rows' => 6],
        ];
        $photoWho = ['name' => 'about_image_4', 'title' => 'Who We Are Photo', 'w' => 16, 'h' => 9, 'max' => '28rem',
            'desc' => 'Photo on the right side of the intro text.',
            'help' => 'At least 1200 px wide gives a sharp result. PNG, JPG, or WebP, max 5 MB.'];

        // 3. Our Story
        $textsStory = [
            ['field' => 'story',       'title' => 'Our Story',     'desc' => 'The story of the studio. A blank line starts a new paragraph.', 'rows' => 8],
            ['field' => 'mission',     'title' => 'Mission',       'desc' => 'One point per line. Each line becomes its own bullet.',         'rows' => 5],
            ['field' => 'vision',      'title' => 'Vision',        'desc' => 'One or two sentences, shown next to the mission.',              'rows' => 3],
        ];
        $photosStory = [
            ['name' => 'about_image_2', 'title' => 'Story Photo 1', 'w' => 3, 'h' => 4, 'max' => '15rem',
             'desc' => 'First photo, on the left of the story.',
             'help' => 'At least 800 px wide gives a sharp result. Max 5 MB.'],
            ['name' => 'about_image_3', 'title' => 'Story Photo 2', 'w' => 3, 'h' => 4, 'max' => '15rem',
             'desc' => 'Second photo. It sits a little lower than the first.',
             'help' => 'At least 800 px wide gives a sharp result. Max 5 MB.'],
        ];

        // 4. Our Approach
        $textsApproach = [
            ['field' => 'impact_statement', 'title' => 'Approach Text', 'desc' => 'Paragraph under the heading "Faster, smarter, more efficient."', 'rows' => 4],
        ];

    @endphp

    {{-- Empty forms. Every text field below points to one of them with the form="..." attribute, so one button saves all texts of a group. --}}
    <form id="texts-home" method="POST" action="{{ route('admin.site.texts.home') }}" hidden>@csrf @method('PUT')</form>
    <form id="texts-about" method="POST" action="{{ route('admin.site.texts.about') }}" hidden>@csrf @method('PUT')</form>

    <div class="site-page">

        <div>
            <p class="site-intro">Change as many texts as you like, then press Save all at the bottom of the Home page or the About page group. Photos are uploaded on their own cards. The About page is listed in the same order as the website, from top to bottom.</p>

            <nav class="site-jump" aria-label="Groups">
                <span class="site-jump-label">Go to</span>
                <a href="#grp-general">General</a>
                <a href="#grp-home">Home page</a>
                <a href="#grp-about">About page</a>
            </nav>
            <nav class="site-jump" aria-label="About page sections">
                <span class="site-jump-label">About</span>
                <a href="#ab-hero">1 Hero</a>
                <a href="#ab-who">2 Who we are</a>
                <a href="#ab-story">3 Story</a>
                <a href="#ab-approach">4 Approach</a>
                <a href="#ab-gallery">5 Gallery</a>
            </nav>
        </div>

        {{-- ===== General ===== --}}
        <div id="grp-general">
            <div class="site-group">
                <h2>General</h2>
                <p>Name, logo, and the background image of the home page hero.</p>
            </div>

            <div class="site-grid site-grid--2">
                <section id="sec-name" class="site-card">
                    <form method="POST" action="{{ route('admin.site.name') }}">
                        @csrf
                        @method('PUT')

                        <div class="site-card-head">
                            <h3>Site Name</h3>
                            <p>Shown in the browser tab title, navbar, and footer.</p>
                        </div>

                        <div class="site-card-body">
                            <x-input-label for="site_name" value="Site Name" />
                            <x-text-input id="site_name" name="site_name" type="text" class="mt-1 block w-full"
                                          :value="old('site_name', $site->site_name)" required />
                            <x-input-error :messages="$errors->get('site_name')" class="mt-1" />
                        </div>

                        <div class="site-card-foot">
                            @if ($msg = $flash('name'))
                                <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                            @endif
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </form>
                </section>

                <x-admin.image-upload
                    name="logo" kind="logo" title="Logo"
                    description="Shown in the navbar and footer. If empty, the site name is shown."
                    :path="$site->logo"
                    :upload-route="route('admin.site.image.upload', 'logo')"
                    :delete-route="route('admin.site.image.destroy', 'logo')"
                    :max-mb="2" :dark="true"
                    help="A transparent PNG is recommended, about 80 px tall. Max 2 MB."
                    :message="$flash('logo')" />
            </div>

            {{-- The hero photo is uploaded as it is. The card next to it chooses which part of the photo stays in view. --}}
            <div class="site-grid site-grid--2" style="margin-top: 1.25rem;">
                <x-admin.image-upload
                    name="hero_image" kind="hero" title="Home Hero Background"
                    description="Background image for the hero section of the home page. If empty, the hero uses a gradient."
                    :path="$site->hero_image"
                    :upload-route="route('admin.site.image.upload', 'hero_image')"
                    :delete-route="route('admin.site.image.destroy', 'hero_image')"
                    :max-mb="5" :dark="true"
                    help="Landscape photo, at least 2000 px wide, max 5 MB. Use the Hero Position card to choose which part stays in view."
                    :message="$flash('hero_image')" />

                <x-admin.focus-point
                    title="Hero Position"
                    description="Choose which part of the hero photo stays in view on desktop and phone."
                    :path="$site->hero_image"
                    :action="route('admin.site.hero-focus')"
                    :x="$site->hero_focus_x ?? 50"
                    :y="$site->hero_focus_y ?? 50"
                    :message="$flash('hero_focus')" />
            </div>
        </div>

        {{-- ===== Home page ===== --}}
        <div id="grp-home">
            <div class="site-group">
                <h2>Home page</h2>
                <p>Text in the hero section. Services, projects, team, clients, and FAQ have their own menus.</p>
            </div>

            <div class="site-grid site-grid--2">
                @foreach ($homeTexts as $t)
                    @include('admin.site._text-field', ['t' => $t, 'site' => $site, 'form' => 'texts-home'])
                @endforeach
            </div>

            <div class="site-savebar">
                @if ($msg = $flash('texts-home'))
                    <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                @else
                    <p class="site-savebar-hint">Saves every text on the Home page.</p>
                @endif
                <button type="submit" form="texts-home" class="btn btn-primary">Save all (Home)</button>
            </div>
        </div>

        {{-- ===== About page ===== --}}
        <div id="grp-about">
            <div class="site-group">
                <h2>About page</h2>
                <p>Sections are listed from top to bottom, exactly like the website. Each section has its text and photo together. Why choose us, the six stages, the team, and clients come from their own menus.</p>
            </div>

            {{-- 1. Hero banner --}}
            <section id="ab-hero" class="site-step">
                <div class="site-step-head">
                    <span class="site-step-num">1</span>
                    <div>
                        <h3>Hero banner</h3>
                        <p>The dark banner at the very top with the title "About Us". Only a background photo can be changed here.</p>
                    </div>
                </div>
                @include('admin.site._photo-card', ['ph' => $photoHero, 'site' => $site, 'flash' => $flash])
            </section>

            {{-- 2. Who we are --}}
            <section id="ab-who" class="site-step">
                <div class="site-step-head">
                    <span class="site-step-num">2</span>
                    <div>
                        <h3>Who we are</h3>
                        <p>The first section under the banner: intro text and a photo on the right.</p>
                    </div>
                </div>
                <div class="site-cols">
                    <div class="site-col">
                        @foreach ($textsWho as $t)
                            @include('admin.site._text-field', ['t' => $t, 'site' => $site, 'form' => 'texts-about'])
                        @endforeach
                    </div>
                    <div class="site-col">
                        @include('admin.site._photo-card', ['ph' => $photoWho, 'site' => $site, 'flash' => $flash])
                    </div>
                </div>
            </section>

            {{-- 3. Our story --}}
            <section id="ab-story" class="site-step">
                <div class="site-step-head">
                    <span class="site-step-num">3</span>
                    <div>
                        <h3>Our story</h3>
                        <p>Two photos on the left, the story with mission and vision on the right.</p>
                    </div>
                </div>
                <div class="site-cols">
                    <div class="site-col">
                        @foreach ($textsStory as $t)
                            @include('admin.site._text-field', ['t' => $t, 'site' => $site, 'form' => 'texts-about'])
                        @endforeach
                    </div>
                    <div class="site-col">
                        @foreach ($photosStory as $ph)
                            @include('admin.site._photo-card', ['ph' => $ph, 'site' => $site, 'flash' => $flash])
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- 4. Our approach --}}
            <section id="ab-approach" class="site-step">
                <div class="site-step-head">
                    <span class="site-step-num">4</span>
                    <div>
                        <h3>Our approach</h3>
                        <p>One paragraph, shown next to three short facts. This section has no photo.</p>
                    </div>
                </div>
                <div class="site-grid site-grid--2">
                    @foreach ($textsApproach as $t)
                        @include('admin.site._text-field', ['t' => $t, 'site' => $site, 'form' => 'texts-about'])
                    @endforeach
                </div>
            </section>

            {{-- 5. Gallery --}}
            <section id="ab-gallery" class="site-step">
                <div class="site-step-head">
                    <span class="site-step-num">5</span>
                    <div>
                        <h3>Life at the studio (gallery)</h3>
                        <p>The four photos of the gallery.</p>
                    </div>
                </div>
                <div>
                    @include('admin.site._about-media', ['site' => $site, 'flash' => $flash, 'part' => 'gallery'])
                </div>
            </section>

            <div class="site-savebar">
                @if ($msg = $flash('texts-about'))
                    <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
                @else
                    <p class="site-savebar-hint">Saves every text on the About page.</p>
                @endif
                <button type="submit" form="texts-about" class="btn btn-primary">Save all (About)</button>
            </div>
        </div>
    </div>

    <script>
        // If there is a validation error, scroll to the first one.
        document.addEventListener('DOMContentLoaded', function () {
            var firstError = document.querySelector('.text-red-600');
            if (firstError) { firstError.scrollIntoView({ block: 'center' }); }
        });
    </script>
</x-admin-layout>