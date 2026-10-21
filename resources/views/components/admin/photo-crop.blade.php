@props([
    'name',
    'title',
    'description' => null,
    'path'        => null,
    'uploadRoute',
    'deleteRoute',
    'w'           => 4,        // frame ratio, width part
    'h'           => 3,        // frame ratio, height part
    'maxWidth'    => '22rem',  // how wide the preview frame can get
    'maxMb'       => 5,
    'long'        => 1600,     // pixels on the long side of the saved photo
    'overlay'     => false,    // true = show the dark hero shade on the preview (preview only, not saved)
    'help'        => null,
    'message'     => null,
])

@php
    $src   = $path ? asset('storage/'.$path) : null;
    $error = $errors->first($name);
@endphp

<section id="sec-{{ $name }}" class="site-card" data-photo-crop
         data-w="{{ $w }}" data-h="{{ $h }}" data-max-mb="{{ $maxMb }}" data-long="{{ $long }}" @if ($src) data-src="{{ $src }}" @endif>
    <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" data-pc-form>
        @csrf

        <div class="site-card-head">
            <h3>{{ $title }}</h3>
            @if ($description)<p>{{ $description }}</p>@endif
        </div>

        <div class="site-card-body">
            <div class="pc-stage {{ $overlay ? 'has-overlay' : '' }}" style="--ratio: {{ $w }} / {{ $h }}; --max: {{ $maxWidth }}">
                <canvas class="pc-canvas" tabindex="0" role="img"
                        aria-label="{{ $title }}. Drag to reposition, or use the arrow keys."></canvas>
                <p class="pc-empty">No photo yet</p>
            </div>

            <label class="pc-tools">
                <span>Zoom</span>
                <input type="range" class="pc-zoom" min="1" max="3" step="0.02" value="1" disabled>
            </label>

            <input type="file" name="{{ $name }}" accept="image/png,image/jpeg,image/webp" class="img-up-file" style="margin-top: 1rem">
            <p class="img-up-hint">Drag the photo to move it, and use the slider or the arrow keys to zoom and nudge. The frame shows exactly what will be saved.@if ($help) {{ $help }}@endif</p>
            <p class="pc-msg img-up-hint" role="status"></p>
            @if ($error)<p class="img-up-error text-red-600">{{ $error }}</p>@endif
        </div>

        <div class="site-card-foot">
            @if ($message)
                <p class="site-note" role="status"><x-admin.icon name="check" class="h-4 w-4" />{{ $message }}</p>
            @endif
            @if ($src)
                <button type="submit" form="pc-del-{{ $name }}" class="btn btn-danger btn-sm">Remove</button>
            @endif
            <button type="submit" class="btn btn-primary btn-sm pc-save" disabled>Save photo</button>
        </div>
    </form>

    @if ($src)
        <form id="pc-del-{{ $name }}" method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Remove this photo?')">
            @csrf
            @method('DELETE')
        </form>
    @endif
</section>

@once
<style>
    .pc-stage { position: relative; width: 100%; max-width: var(--max, 22rem); margin-inline: auto; aspect-ratio: var(--ratio, 4 / 3); overflow: hidden; background: #eeeaf7; border: 1px solid var(--color-border); border-radius: var(--radius); }
    .pc-canvas { display: none; width: 100%; height: 100%; cursor: grab; touch-action: none; }
    .pc-canvas:active { cursor: grabbing; }
    .pc-canvas:focus-visible { outline: 3px solid var(--color-primary); outline-offset: -3px; }
    .pc-stage.is-ready .pc-canvas { display: block; }
    .pc-empty { position: absolute; inset: 0; display: grid; place-items: center; margin: 0; padding: 0 1rem; font-size: var(--fs-small); text-align: center; color: var(--color-muted); }
    .pc-stage.is-ready .pc-empty { display: none; }
    /* Same shade as the home hero. It sits on top of the photo but lets clicks and drags through. */
    .pc-stage.has-overlay::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0.55) 55%, rgba(0, 0, 0, 0.25) 100%); pointer-events: none; }
    .pc-stage.has-overlay:not(.is-ready)::after { display: none; }
    .pc-tools { display: flex; align-items: center; gap: 0.75rem; width: 100%; max-width: var(--max, 22rem); margin: 0.9rem auto 0; font-size: var(--fs-small); color: var(--color-muted); }
    .pc-tools .pc-zoom { flex: 1; accent-color: var(--color-primary); }
    .pc-msg:empty { display: none; }
</style>

<script>
    /* Photo framing. The photo is drawn on a canvas that has the same ratio as the frame on the website.
       Dragging, the arrow keys, and the zoom slider move the photo. When you save, the framed area is
       exported and put into the file input, so what you see is what is uploaded. */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-photo-crop]').forEach(function (root) {
            var w = Number(root.dataset.w), h = Number(root.dataset.h);
            var LONG = Number(root.dataset.long) || 1600;
            var limit = (Number(root.dataset.maxMb) || 5) * 1048576;
            var cw = w >= h ? LONG : Math.round(LONG * w / h);
            var ch = w >= h ? Math.round(LONG * h / w) : LONG;

            var stage = root.querySelector('.pc-stage');
            var canvas = root.querySelector('.pc-canvas');
            var input = root.querySelector('input[type=file]');
            var zoomEl = root.querySelector('.pc-zoom');
            var save = root.querySelector('.pc-save');
            var form = root.querySelector('[data-pc-form]');
            var msg = root.querySelector('.pc-msg');

            canvas.width = cw;
            canvas.height = ch;
            var ctx = canvas.getContext('2d');

            var img = null, type = 'image/jpeg', name = 'photo', dirty = false, sending = false;
            var view = { zoom: 1, x: 0.5, y: 0.5 };
            var clamp = function (n) { return Math.min(1, Math.max(0, n)); };
            var say = function (text) { msg.textContent = text || ''; };

            function layout() {
                var s = Math.max(cw / img.naturalWidth, ch / img.naturalHeight) * view.zoom;
                var dw = img.naturalWidth * s, dh = img.naturalHeight * s;
                return { dw: dw, dh: dh, dx: (cw - dw) * view.x, dy: (ch - dh) * view.y };
            }

            function draw() {
                var l = layout();
                ctx.clearRect(0, 0, cw, ch);
                ctx.drawImage(img, l.dx, l.dy, l.dw, l.dh);
            }

            function markDirty() {
                dirty = true;
                save.disabled = false;
            }

            function show(el) {
                img = el;
                view.zoom = 1; view.x = 0.5; view.y = 0.5;
                zoomEl.value = 1;
                zoomEl.disabled = false;
                stage.classList.add('is-ready');
                draw();
            }

            // A photo that is already saved. Loaded from this same site, so it can be re-exported.
            if (root.dataset.src) {
                var u = new URL(root.dataset.src, window.location.href);
                var saved = new Image();
                saved.onload = function () {
                    type = /\.png$/i.test(u.pathname) ? 'image/png' : 'image/jpeg';
                    show(saved);
                };
                saved.src = u.pathname + u.search;
            }

            input.addEventListener('change', function () {
                var file = input.files[0];
                say('');
                if (!file) { return; }
                if (file.size > limit) {
                    input.value = '';
                    say('That file is larger than ' + (limit / 1048576) + ' MB. Please choose a smaller one.');
                    return;
                }
                var el = new Image();
                el.onload = function () {
                    type = file.type;
                    name = file.name.replace(/\.[^.]+$/, '') || 'photo';
                    show(el);
                    markDirty();
                };
                el.onerror = function () { input.value = ''; say('That file could not be read as an image.'); };
                el.src = URL.createObjectURL(file);
            });

            zoomEl.addEventListener('input', function () {
                if (!img) { return; }
                view.zoom = Number(zoomEl.value);
                draw();
                markDirty();
            });

            canvas.addEventListener('pointerdown', function (event) {
                if (!img) { return; }
                event.preventDefault();
                var k = cw / canvas.getBoundingClientRect().width;
                var start = layout();
                var from = { x: event.clientX, y: event.clientY };
                canvas.setPointerCapture(event.pointerId);

                function move(e) {
                    var l = layout();
                    if (cw - l.dw < 0) { view.x = clamp((start.dx + (e.clientX - from.x) * k) / (cw - l.dw)); }
                    if (ch - l.dh < 0) { view.y = clamp((start.dy + (e.clientY - from.y) * k) / (ch - l.dh)); }
                    draw();
                    markDirty();
                }
                function end() {
                    canvas.removeEventListener('pointermove', move);
                    canvas.removeEventListener('pointerup', end);
                    canvas.removeEventListener('pointercancel', end);
                }
                canvas.addEventListener('pointermove', move);
                canvas.addEventListener('pointerup', end);
                canvas.addEventListener('pointercancel', end);
            });

            canvas.addEventListener('keydown', function (event) {
                var step = { ArrowLeft: [0.05, 0], ArrowRight: [-0.05, 0], ArrowUp: [0, 0.05], ArrowDown: [0, -0.05] }[event.key];
                if (!img || !step) { return; }
                event.preventDefault();
                view.x = clamp(view.x + step[0]);
                view.y = clamp(view.y + step[1]);
                draw();
                markDirty();
            });

            function toBlob(c, t, q) {
                return new Promise(function (resolve, reject) {
                    try { c.toBlob(function (b) { b ? resolve(b) : reject(new Error('blob')); }, t, q); } catch (e) { reject(e); }
                });
            }

            // PNG stays PNG if it fits the size limit. Everything else becomes a JPEG on a white base.
            function exportFile() {
                var png = type === 'image/png' ? toBlob(canvas, 'image/png') : Promise.resolve(null);
                return png.then(function (blob) {
                    if (blob && blob.size <= limit * 0.95) { return new File([blob], name + '.png', { type: 'image/png' }); }

                    var flat = document.createElement('canvas');
                    flat.width = cw; flat.height = ch;
                    var fx = flat.getContext('2d');
                    fx.fillStyle = '#fff';
                    fx.fillRect(0, 0, cw, ch);
                    fx.drawImage(canvas, 0, 0);

                    var q = 0.92;
                    function attempt() {
                        return toBlob(flat, 'image/jpeg', q).then(function (b) {
                            if (b.size > limit * 0.95 && q > 0.5) { q -= 0.1; return attempt(); }
                            return new File([b], name + '.jpg', { type: 'image/jpeg' });
                        });
                    }
                    return attempt();
                });
            }

            form.addEventListener('submit', function (event) {
                if (sending) { return; }
                event.preventDefault();
                if (!img || !dirty) { return; }

                save.disabled = true;
                say('Preparing the photo…');
                exportFile().then(function (file) {
                    var data = new DataTransfer();
                    data.items.add(file);
                    input.files = data.files;
                    sending = true;
                    form.submit();
                }).catch(function () {
                    save.disabled = false;
                    say('This photo could not be processed. Choose it again from your device and try once more.');
                });
            });
        });
    });
</script>
@endonce