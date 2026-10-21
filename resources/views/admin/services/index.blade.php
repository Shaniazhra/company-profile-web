<x-admin-layout>
    <style>
        /* Same page padding, header, and card styles as the Team page, so the admin feels like one product. */
        .pa { width: 100%; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .pa > * + * { margin-top: 1.5rem; }

        .pa-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-border); }
        .pa-head h2 { margin: 0; font-size: var(--fs-h3); }
        .pa-head p { margin: 0.25rem 0 0; font-size: var(--fs-small); color: var(--color-muted); }

        .pa-card { overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .pa-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); }
        .pa-card-head h3 { margin: 0; font-size: var(--fs-h4); }
        .pa-card-head p { margin: 0; font-size: var(--fs-small); color: var(--color-muted); }

        /* Service list: drag the handle to change the order */
        .tl { margin: 0; padding: 0; list-style: none; }
        .tl-item { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1rem; padding: 0.8rem 1.5rem; background: var(--color-surface); }
        .tl-item + .tl-item { border-top: 1px solid var(--color-border); }
        .tl-item.is-dragging { position: relative; z-index: 2; background: color-mix(in srgb, var(--color-primary) 8%, white); box-shadow: 0 14px 28px -18px rgba(36, 26, 74, 0.55); }
        .tl-handle { flex: none; display: grid; place-items: center; width: 2.25rem; height: 2.5rem; padding: 0; color: var(--color-muted); background: transparent; border: 0; border-radius: var(--radius); cursor: grab; touch-action: none; }
        .tl-handle:hover { color: var(--color-primary); background: color-mix(in srgb, var(--color-primary) 8%, white); }
        .tl-handle:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }
        .tl-handle svg { width: 1.1rem; height: 1.1rem; }
        .is-dragging .tl-handle { cursor: grabbing; }
        .tl-icon { flex: none; display: grid; place-items: center; width: 3rem; height: 3rem; color: var(--color-primary); background: color-mix(in srgb, var(--color-primary) 8%, white); border-radius: var(--radius); }
        .tl-icon svg { width: 1.5rem; height: 1.5rem; }
        .tl-photo { flex: none; width: 5rem; height: 3rem; overflow: hidden; border-radius: var(--radius); background: color-mix(in srgb, var(--color-primary) 8%, white); }
        .tl-photo img { display: block; width: 100%; height: 100%; object-fit: cover; }
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

        @media (max-width: 760px) {
            .pa-card-head, .tl-item, .tl-status { padding-inline: 1rem; }
        }
    </style>

    <div class="pa">
        <div class="pa-head">
            <div>
                <h2>Services</h2>
                <p>Animation skills shown to potential clients. The order here matches the order on the website.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">+ Add Service</a>
        </div>

        <x-admin.flash />

        <section class="pa-card">
            <div class="pa-card-head">
                <h3>All services</h3>
                <p>Drag the handle on the left to change the order. It saves by itself.</p>
            </div>

            @if ($services->isEmpty())
                <p class="tl-empty">No services yet. Click "Add Service" to get started.</p>
            @endif

            @if ($services->isNotEmpty())
                <ul class="tl" data-sortable data-url="{{ route('admin.services.order') }}" data-token="{{ csrf_token() }}">
                    @foreach ($services as $service)
                        <li class="tl-item" data-item data-id="{{ $service->id }}">
                            <button type="button" class="tl-handle" data-handle
                                    aria-label="Move {{ $service->getTranslation('title', 'en') }}. Drag it, or press the Up and Down arrow keys.">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="6" r="1.6"/><circle cx="15" cy="6" r="1.6"/><circle cx="9" cy="12" r="1.6"/><circle cx="15" cy="12" r="1.6"/><circle cx="9" cy="18" r="1.6"/><circle cx="15" cy="18" r="1.6"/></svg>
                            </button>

                            <span class="tl-icon">{{ \App\Support\Icons::svg($service->icon, 'h-6 w-6') }}</span>

                            @if ($service->image)
                                <span class="tl-photo"><img src="{{ asset('storage/'.$service->image) }}" alt="" loading="lazy"></span>
                            @endif

                            <div class="tl-main">
                                <p class="tl-name">{{ $service->getTranslation('title', 'en') }}</p>
                                <p class="tl-role">{{ $service->getTranslation('summary', 'en', false) ?: 'No summary yet' }}</p>
                            </div>

                            <span class="tl-pill {{ $service->is_active ? 'is-live' : '' }}">{{ $service->is_active ? 'Shown' : 'Hidden' }}</span>

                            <div class="tl-actions">
                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete this service? Its image will be deleted too.') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="tl-status" data-order-status role="status" aria-live="polite">Order is saved when you drop a service in a new place.</p>
            @endif
        </section>
    </div>

    <script>
        // Order: drag the handle up or down (mouse or touch), or focus it and press the arrow keys. The new order is saved right away.
        document.addEventListener('DOMContentLoaded', function () {
            var list = document.querySelector('[data-sortable]');
            if (!list) { return; }
            var status = document.querySelector('[data-order-status]');
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
    </script>
</x-admin-layout>