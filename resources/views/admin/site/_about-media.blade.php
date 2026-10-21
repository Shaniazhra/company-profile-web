{{--
    About page media: behind-the-scenes video and photo gallery.
    Included from edit.blade.php, one part at a time:
        @include('admin.site._about-media', ['site' => $site, 'flash' => $flash, 'part' => 'video'])
        @include('admin.site._about-media', ['site' => $site, 'flash' => $flash, 'part' => 'gallery'])
    $galleryPhotos comes from SiteSettingController@edit.
--}}
@php
    $galleryCount = $galleryPhotos->count();
    $video = $site->bts_video ? asset('storage/'.$site->bts_video) : null;
    $maxVideoMb = 50;
@endphp

@once
<style>
    .bts-preview { display: block; width: 100%; max-width: 32rem; aspect-ratio: 16 / 9; margin-inline: auto; background: #000; border: 1px solid var(--color-border); border-radius: var(--radius); }
    .bts-empty { display: grid; place-items: center; width: 100%; max-width: 32rem; aspect-ratio: 16 / 9; margin-inline: auto; font-size: var(--fs-small); color: var(--color-muted); background: #eeeaf7; border: 1px solid var(--color-border); border-radius: var(--radius); }
    .bts-form { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-top: 1rem; }
    .bts-form + .bts-form { margin-top: 0.5rem; }
    .bts-file { max-width: 100%; font-size: var(--fs-small); color: var(--color-muted); }
    .bts-file::file-selector-button { margin-right: 0.75rem; padding: 0.5rem 1rem; font: inherit; font-weight: 500; color: var(--color-primary); background: #fff; border: var(--border-width, 1px) solid var(--color-primary); border-radius: var(--radius); cursor: pointer; }
    .bts-file::file-selector-button:hover { color: #fff; background: var(--color-primary); }
    .bts-hint { margin: 0.75rem 0 0; font-size: var(--fs-caption); color: var(--color-muted); }
    .bts-error { margin: 0.5rem 0 0; font-size: var(--fs-small); color: var(--color-danger, #b3261e); }
    .bts-state { display: inline-flex; align-items: center; margin-right: auto; padding: 0.15rem 0.7rem; font-size: var(--fs-caption); font-weight: 600; border-radius: 999px; }
    .bts-state.is-on { color: #1d7a55; background: #e2f4ec; }
    .bts-state.is-off { color: #7a5a1d; background: #f7efd9; }

    .gal-list { display: grid; gap: 0.75rem; margin: 1.25rem 0 0; padding: 0; list-style: none; }
    .gal-row { display: grid; grid-template-columns: auto 6rem minmax(0, 1fr) auto; gap: 1rem; align-items: start; padding: 0.9rem; border: 1px solid var(--color-border); border-radius: var(--radius); }
    .gal-row { background: #fff; }
    .gal-row.is-off { background: #f6f4fb; }
    .gal-row.is-dragging { position: relative; z-index: 2; background: #fff; border-color: var(--color-primary); box-shadow: 0 14px 30px -14px rgba(36, 26, 74, 0.5); opacity: 0.95; }
    .gal-grip { display: grid; place-items: center; align-self: center; width: 2rem; height: 2.75rem; padding: 0; color: var(--color-muted); background: none; border: 0; border-radius: calc(var(--radius) * 0.75); cursor: grab; touch-action: none; }
    .gal-grip:hover { color: var(--color-primary); background: #eeeaf7; }
    .gal-grip:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
    .gal-grip svg { width: 1.1rem; height: 1.1rem; fill: currentColor; }
    body.is-sorting { cursor: grabbing; user-select: none; }
    body.is-sorting .gal-grip { cursor: grabbing; }
    .gal-row.is-extra { opacity: 0.6; }
    .gal-thumb { position: relative; width: 6rem; aspect-ratio: 4 / 3; overflow: hidden; background: #eeeaf7; border-radius: calc(var(--radius) * 0.75); }
    .gal-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .gal-row.is-off .gal-thumb img { opacity: 0.45; }
    .gal-num { position: absolute; top: 0.25rem; left: 0.25rem; min-width: 1.4rem; padding: 0 0.35rem; font-size: var(--fs-caption); font-weight: 700; line-height: 1.4rem; text-align: center; color: #fff; background: var(--color-primary); border-radius: 999px; }
    .gal-edit { display: grid; gap: 0.6rem; min-width: 0; }
    .gal-edit .gal-line { display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem 1rem; }
    .gal-check { display: inline-flex; align-items: center; gap: 0.4rem; font-size: var(--fs-small); color: var(--color-muted); }
    .gal-warn { font-size: var(--fs-caption); font-weight: 600; color: #9a5b00; }
    .gal-side { display: flex; flex-direction: column; gap: 0.4rem; }
    .gal-side form { margin: 0; }
    .gal-side .btn { width: 100%; }
    @media (max-width: 720px) {
        .gal-row { grid-template-columns: auto 5rem minmax(0, 1fr); }
        .gal-thumb { width: 5rem; }
        .gal-side { grid-column: 1 / -1; flex-direction: row; flex-wrap: wrap; }
        .gal-edit { grid-column: 3; }
        .gal-side .btn { width: auto; }
    }
</style>
@endonce

{{-- ===== Behind the scenes: video + poster ===== --}}
@if ($part === 'video')
    <section id="sec-bts" class="site-card">
        <div class="site-card-head">
            <h3>Video</h3>
            <p>MP4 or WebM, up to {{ $maxVideoMb }} MB. It never plays by itself, and it only loads when a visitor presses play.</p>
        </div>

        <div class="site-card-body">
            @if ($video)
                <video class="bts-preview" controls preload="metadata" playsinline @if ($site->bts_poster) poster="{{ asset('storage/'.$site->bts_poster) }}" @endif>
                    <source src="{{ $video }}">
                </video>
            @else
                <div class="bts-empty">No video yet</div>
            @endif

            <form method="POST" action="{{ route('admin.site.video.upload') }}" enctype="multipart/form-data" class="bts-form">
                @csrf
                <input type="file" name="bts_video" accept="video/mp4,video/webm" class="bts-file" aria-label="Choose a video file">
                <button type="submit" class="btn btn-primary btn-sm">{{ $video ? 'Replace video' : 'Upload video' }}</button>
            </form>

            @if ($video)
                <form method="POST" action="{{ route('admin.site.video.destroy') }}" class="bts-form" onsubmit="return confirm('Remove this video?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline btn-sm">Remove video</button>
                </form>
            @endif

            @error('bts_video')
                <p class="bts-error" role="alert">{{ $message }}</p>
            @enderror

            <p class="bts-hint">Use H.264 MP4 for the widest support. Your server must allow uploads this large: set <code>upload_max_filesize</code> and <code>post_max_size</code> in php.ini to at least {{ $maxVideoMb }}M.</p>
        </div>

        <div class="site-card-foot">
            @if ($msg = $flash('bts'))
                <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
            @endif
            <span class="bts-state {{ $site->bts_enabled ? 'is-on' : 'is-off' }}">{{ $site->bts_enabled ? 'Video shown' : 'Video hidden' }}</span>
            <form method="POST" action="{{ route('admin.site.section', 'bts') }}">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-outline btn-sm">{{ $site->bts_enabled ? 'Hide video' : 'Show video' }}</button>
            </form>
        </div>
    </section>

    <x-admin.image-upload
        name="bts_poster" kind="hero" title="Video Poster"
        description="The picture shown on the video before it is played. If empty, the browser shows the first frame or a black box."
        :path="$site->bts_poster"
        :upload-route="route('admin.site.image.upload', 'bts_poster')"
        :delete-route="route('admin.site.image.destroy', 'bts_poster')"
        :max-mb="5" :dark="true"
        help="Landscape 16:9, at least 1280 px wide. PNG, JPG, or WebP, max 5 MB."
        :message="$flash('bts_poster')" />
@endif

{{-- ===== Photo gallery ===== --}}
@if ($part === 'gallery')
<section id="sec-gallery" class="site-card">
    <div class="site-card-head">
        <h3>Photos</h3>
        <p>The website shows the first 4 photos that are switched on. Drag the handle on the left of a photo to change the order.</p>
    </div>

    <div class="site-card-body">
        {{-- Add photos --}}
        <form method="POST" action="{{ route('admin.about-photos.store') }}" enctype="multipart/form-data" class="bts-form" style="margin-top: 0;">
            @csrf
            <input type="file" name="photos[]" accept="image/png,image/jpeg,image/webp" multiple class="bts-file" aria-label="Choose photos to add">
            <x-text-input name="alt" type="text" maxlength="160" placeholder="Alt text for these photos (optional)" class="block" style="max-width: 22rem;" aria-label="Alt text for the new photos" />
            <button type="submit" class="btn btn-primary btn-sm">Add photos</button>
        </form>
        <p class="bts-hint">You can choose up to 20 photos at once. PNG, JPG, or WebP, max 5 MB each. At least 1200 px wide is sharp enough.</p>

        @foreach ($errors->getBag('gallery')->all() as $error)
            <p class="bts-error" role="alert">{{ $error }}</p>
        @endforeach

        {{-- Photos --}}
        @if ($galleryCount)
            @php $shown = 0; @endphp
            <ul class="gal-list" id="gal-list" data-order-url="{{ route('admin.about-photos.order') }}" data-token="{{ csrf_token() }}">
                @foreach ($galleryPhotos as $photo)
                    @php
                        $bag = $errors->getBag('gallery_'.$photo->id);
                        if ($photo->is_active) { $shown++; }
                        $isExtra = $photo->is_active && $shown > 4;
                    @endphp
                    <li class="gal-row {{ $photo->is_active ? '' : 'is-off' }} {{ $isExtra ? 'is-extra' : '' }}" data-id="{{ $photo->id }}" data-active="{{ $photo->is_active ? 1 : 0 }}">
                        <button type="button" class="gal-grip" aria-label="Drag to change the order of photo {{ $loop->iteration }}" title="Drag to reorder">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="5" r="1.7"/><circle cx="15" cy="5" r="1.7"/><circle cx="9" cy="12" r="1.7"/><circle cx="15" cy="12" r="1.7"/><circle cx="9" cy="19" r="1.7"/><circle cx="15" cy="19" r="1.7"/></svg>
                        </button>
                        <div class="gal-thumb">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->alt }}" loading="lazy">
                            <span class="gal-num" aria-label="Position {{ $loop->iteration }}">{{ $loop->iteration }}</span>
                        </div>

                        <form method="POST" action="{{ route('admin.about-photos.update', $photo) }}" enctype="multipart/form-data" class="gal-edit" id="gal-form-{{ $photo->id }}">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="alt-{{ $photo->id }}" class="gal-check" style="display:block; margin-bottom: 0.25rem;">Alt text</label>
                                <x-text-input id="alt-{{ $photo->id }}" name="alt" type="text" maxlength="160" class="block w-full"
                                              :value="old('alt', $photo->alt)" placeholder="Describe what the photo shows" />
                                @unless (filled($photo->alt))
                                    <span class="gal-warn">Add alt text so screen readers can describe this photo.</span>
                                @endunless
                                <span class="gal-warn gal-extra-warn" @unless ($isExtra) hidden @endunless>Not shown: the website only uses the first 4 photos.</span>
                            </div>
                            <div class="gal-line">
                                <input type="hidden" name="is_active" value="0">
                                <label class="gal-check"><input type="checkbox" name="is_active" value="1" @checked($photo->is_active)> Show on website</label>
                                <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" class="bts-file" aria-label="Replace this photo">
                            </div>
                            @foreach ($bag->all() as $error)
                                <p class="bts-error" role="alert">{{ $error }}</p>
                            @endforeach
                        </form>

                        <div class="gal-side">
                            <button type="submit" form="gal-form-{{ $photo->id }}" class="btn btn-primary btn-sm">Save</button>

                            <form method="POST" action="{{ route('admin.about-photos.destroy', $photo) }}" onsubmit="return confirm('Delete this photo? The file will be removed too.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm">Delete</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p class="bts-hint" id="gal-status" role="status" aria-live="polite"></p>

            <script>
                /* Drag and drop for the gallery order. Works with mouse and touch; ArrowUp / ArrowDown on a focused handle also move a photo. */
                (function () {
                    var list = document.getElementById('gal-list');
                    if (!list) { return; }
                    var status = document.getElementById('gal-status');
                    var dragging = null;
                    var before = [];

                    function rows() { return Array.prototype.slice.call(list.querySelectorAll('.gal-row')); }
                    function ids() { return rows().map(function (r) { return Number(r.dataset.id); }); }
                    function say(text, bad) { status.textContent = text; status.style.color = bad ? 'var(--color-danger, #b3261e)' : ''; }

                    // Numbers, and the note on photos beyond the 4th that are switched on
                    function refresh() {
                        var shown = 0;
                        rows().forEach(function (row, i) {
                            var num = row.querySelector('.gal-num');
                            if (num) { num.textContent = i + 1; num.setAttribute('aria-label', 'Position ' + (i + 1)); }
                            var grip = row.querySelector('.gal-grip');
                            if (grip) { grip.setAttribute('aria-label', 'Drag to change the order of photo ' + (i + 1)); }
                            var active = row.dataset.active === '1';
                            if (active) { shown++; }
                            var extra = active && shown > 4;
                            row.classList.toggle('is-extra', extra);
                            var warn = row.querySelector('.gal-extra-warn');
                            if (warn) { warn.hidden = !extra; }
                        });
                    }

                    function restore(order) {
                        order.forEach(function (id) {
                            var row = list.querySelector('.gal-row[data-id="' + id + '"]');
                            if (row) { list.appendChild(row); }
                        });
                        refresh();
                    }

                    function save(previous) {
                        say('Saving order...');
                        fetch(list.dataset.orderUrl, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': list.dataset.token },
                            body: JSON.stringify({ ids: ids() })
                        }).then(function (res) {
                            if (!res.ok) { throw new Error('failed'); }
                            say('Order saved.');
                        }).catch(function () {
                            restore(previous);
                            say('The order could not be saved. Please try again.', true);
                        });
                    }

                    function sameOrder(a, b) { return a.length === b.length && a.every(function (v, i) { return v === b[i]; }); }

                    list.addEventListener('pointerdown', function (e) {
                        var grip = e.target.closest('.gal-grip');
                        if (!grip || (e.pointerType === 'mouse' && e.button !== 0)) { return; }
                        e.preventDefault();
                        dragging = grip.closest('.gal-row');
                        before = ids();
                        dragging.classList.add('is-dragging');
                        document.body.classList.add('is-sorting');
                    });

                    document.addEventListener('pointermove', function (e) {
                        if (!dragging) { return; }
                        var others = rows().filter(function (r) { return r !== dragging; });
                        var target = null;
                        for (var i = 0; i < others.length; i++) {
                            var box = others[i].getBoundingClientRect();
                            if (e.clientY < box.top + box.height / 2) { target = others[i]; break; }
                        }
                        if (target) {
                            if (dragging.nextElementSibling !== target) { list.insertBefore(dragging, target); }
                        } else if (list.lastElementChild !== dragging) {
                            list.appendChild(dragging);
                        }
                        // Scroll the page when the pointer is near the top or bottom edge
                        if (e.clientY < 70) { window.scrollBy(0, -14); }
                        else if (e.clientY > window.innerHeight - 70) { window.scrollBy(0, 14); }
                        refresh();
                    });

                    function finish() {
                        if (!dragging) { return; }
                        dragging.classList.remove('is-dragging');
                        document.body.classList.remove('is-sorting');
                        dragging = null;
                        if (!sameOrder(before, ids())) { save(before); }
                    }
                    document.addEventListener('pointerup', finish);
                    document.addEventListener('pointercancel', finish);

                    list.addEventListener('keydown', function (e) {
                        var grip = e.target.closest('.gal-grip');
                        if (!grip || (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) { return; }
                        e.preventDefault();
                        var row = grip.closest('.gal-row');
                        var previous = ids();
                        if (e.key === 'ArrowUp' && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
                        else if (e.key === 'ArrowDown' && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
                        else { return; }
                        refresh();
                        grip.focus();
                        save(previous);
                    });
                })();
            </script>
        @else
            <p class="bts-hint" style="margin-top: 1.25rem;">No photos yet. The website shows 4 placeholder boxes until you add photos.</p>
        @endif
    </div>

    <div class="site-card-foot">
        @if ($msg = $flash('gallery'))
            <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $msg }}</p>
        @endif
        <span class="bts-state {{ $site->gallery_enabled ? 'is-on' : 'is-off' }}">{{ $site->gallery_enabled ? 'Photos shown' : 'Photos hidden' }}</span>
        <form method="POST" action="{{ route('admin.site.section', 'gallery') }}">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-outline btn-sm">{{ $site->gallery_enabled ? 'Hide photos' : 'Show photos' }}</button>
        </form>
    </div>
</section>
@endif