<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $item->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
<div>
    <x-input-label for="role" value="Role" />
    <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="old('role', $item->role)" required />
    <x-input-error :messages="$errors->get('role')" class="mt-1" />
</div>
<div>
    <x-input-label for="team_division_id" value="Division" />
    <select id="team_division_id" name="team_division_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">No division</option>
        @foreach ($divisions as $division)
            <option value="{{ $division->id }}" @selected((string) old('team_division_id', $item->team_division_id) === (string) $division->id)>{{ $division->name }}</option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">Divisions are added on the Team list page. The Team page on the website groups people by division.</p>
    <x-input-error :messages="$errors->get('team_division_id')" class="mt-1" />
</div>
<x-admin.image-field name="photo" label="Photo" :path="$item->photo" help="A PNG with a transparent background looks best. Max 2 MB." />

{{-- Preview of the card on the website. Drag the photo to position it, use the slider to zoom. --}}
<div data-framing>
    <x-input-label value="Photo position" />
    <style>
        .pf { display: grid; gap: 0.75rem; max-width: 16rem; margin-top: 0.5rem; }
        .pf-card { --tab: 48%; position: relative; isolation: isolate; aspect-ratio: 3 / 4; overflow: hidden; border-radius: 1.5rem 1.5rem 0.5rem 0.5rem; background: #f3f0fa; cursor: grab; touch-action: none; }
        .pf-card:active { cursor: grabbing; }
        .pf-card:focus-visible { outline: 3px solid #6366f1; outline-offset: 2px; }
        .pf-card::before { content: ""; position: absolute; z-index: 0; top: 0; bottom: 0; left: 0; width: var(--tab); border-radius: 1.5rem 1.5rem 0 0; background: linear-gradient(170deg, #8b2fd0 0%, #5b1f8e 100%); }
        .pf-card img { position: absolute; z-index: 1; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: var(--fx, 50%) var(--fy, 100%); transform: scale(var(--z, 1)); transform-origin: var(--fx, 50%) var(--fy, 100%); pointer-events: none; user-select: none; }
        .pf-card::after { content: ""; position: absolute; z-index: 2; right: 0; bottom: 0; left: 0; height: 40%; background: linear-gradient(to top, rgba(139, 47, 208, 1) 0%, rgba(139, 47, 208, 0.9) 30%, rgba(139, 47, 208, 0) 100%); pointer-events: none; }
        .pf-info { position: absolute; z-index: 3; right: 1rem; bottom: 1rem; left: 1rem; color: #fff; pointer-events: none; }
        .pf-info strong { display: block; font-size: 1.05rem; line-height: 1.2; }
        .pf-info span { display: block; margin-top: 0.2rem; font-size: 0.85rem; opacity: 0.9; }
        .pf-empty { position: absolute; z-index: 1; inset: 0; display: grid; place-items: center; padding: 1rem; text-align: center; font-size: 0.85rem; color: #6b6785; }
        .pf-zoom { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #4b5563; }
        .pf-zoom input[type="range"] { flex: 1; min-width: 0; }
        .pf-zoom output { min-width: 3rem; text-align: right; font-variant-numeric: tabular-nums; }
        .pf-reset { padding: 0.25rem 0.75rem; font-size: 0.85rem; color: #374151; background: #fff; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; }
        .pf-reset:hover { background: #f3f4f6; }
    </style>
    <div class="pf">
        <div class="pf-card" data-frame tabindex="0" role="group"
             aria-label="Photo preview. Drag, or use the arrow keys, to choose which part stays in view."
             style="--fx: {{ old('photo_x', $item->photo_x ?? 50) }}%; --fy: {{ old('photo_y', $item->photo_y ?? 100) }}%; --z: {{ old('photo_zoom', $item->photo_zoom ?? 1) }};">
            <img data-img src="{{ $item->photo ? asset('storage/'.$item->photo) : '' }}" alt="" draggable="false" @unless ($item->photo) hidden @endunless>
            <span class="pf-empty" data-empty @if ($item->photo) hidden @endif>Choose a photo to position it</span>
            <div class="pf-info"><strong data-name>{{ old('name', $item->name) ?: 'Name' }}</strong><span data-role>{{ old('role', $item->role) ?: 'Role' }}</span></div>
        </div>
        <div class="pf-zoom">
            <label for="photo-zoom">Zoom</label>
            <input type="range" id="photo-zoom" min="1" max="3" step="0.05" value="{{ old('photo_zoom', $item->photo_zoom ?? 1) }}" data-zoom>
            <output data-zoom-out></output>
            <button type="button" class="pf-reset" data-reset>Reset</button>
        </div>
        <input type="hidden" name="photo_x" value="{{ old('photo_x', $item->photo_x ?? 50) }}" data-fx>
        <input type="hidden" name="photo_y" value="{{ old('photo_y', $item->photo_y ?? 100) }}" data-fy>
        <input type="hidden" name="photo_zoom" value="{{ old('photo_zoom', $item->photo_zoom ?? 1) }}" data-z>
    </div>
    <p class="mt-1 text-xs text-gray-500">This is how the card looks on the website. Drag the photo to choose which part stays in view, and slide to zoom in or out.</p>
</div>

<div>
    <x-input-label for="credits" value="Credits (optional)" />
    <textarea id="credits" name="credits" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('credits', $item->credits) }}</textarea>
    <p class="mt-1 text-xs text-gray-500">One credit per line.</p>
    <x-input-error :messages="$errors->get('credits')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="is_featured" value="0">
        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300"
               @checked(old('is_featured', $item->is_featured))>
        Show on the Home and About pages (up to 3 people)
    </label>
    <x-input-error :messages="$errors->get('is_featured')" class="mt-1" />
</div>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.team.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-framing]');
        if (!root) { return; }
        var frame = root.querySelector('[data-frame]');
        var img = root.querySelector('[data-img]');
        var empty = root.querySelector('[data-empty]');
        var inX = root.querySelector('[data-fx]');
        var inY = root.querySelector('[data-fy]');
        var inZ = root.querySelector('[data-z]');
        var slider = root.querySelector('[data-zoom]');
        var out = root.querySelector('[data-zoom-out]');
        var st = { x: parseFloat(inX.value), y: parseFloat(inY.value), z: parseFloat(inZ.value) };
        if (isNaN(st.x)) { st.x = 50; } if (isNaN(st.y)) { st.y = 100; } if (isNaN(st.z)) { st.z = 1; }
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

        // How far the picture can move: the part outside the frame, plus the extra size from zooming.
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
        root.querySelector('[data-reset]').addEventListener('click', function () { st.x = 50; st.y = 100; st.z = 1; apply(); });

        // A new photo shows in the preview right away, with the position reset.
        var file = document.querySelector('input[type="file"][name="photo"]');
        if (file) {
            file.addEventListener('change', function () {
                var f = file.files[0];
                if (!f) { return; }
                img.src = URL.createObjectURL(f);
                img.hidden = false;
                if (empty) { empty.hidden = true; }
                st.x = 50; st.y = 100; st.z = 1;
                apply();
            });
        }

        // Name and role in the preview follow what is typed.
        [['name', '[data-name]', 'Name'], ['role', '[data-role]', 'Role']].forEach(function (pair) {
            var input = document.getElementById(pair[0]);
            var target = root.querySelector(pair[1]);
            if (input && target) {
                input.addEventListener('input', function () { target.textContent = input.value || pair[2]; });
            }
        });

        apply();
    });
</script>