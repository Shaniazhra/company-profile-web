@php
    $firstName = \Illuminate\Support\Str::of(auth()->user()->name)->before(' ');
    $siteUrl   = route('home', ['locale' => 'id']);

    $stats = [
        ['label' => 'Layanan',     'icon' => 'services', 'route' => 'admin.services.index', 'total' => $counts['services']['total'], 'note' => $counts['services']['live'].' tampil'],
        ['label' => 'Proyek',      'icon' => 'projects', 'route' => 'admin.projects.index', 'total' => $counts['projects']['total'], 'note' => $counts['projects']['live'].' tayang'],
        ['label' => 'Klien',       'icon' => 'clients',  'route' => 'admin.clients.index',  'total' => $counts['clients']['total'],  'note' => $counts['clients']['live'].' tampil'],
        ['label' => 'Anggota Tim', 'icon' => 'team',     'route' => 'admin.team.index',     'total' => $counts['members']['total'],  'note' => $counts['divisions'].' divisi', 'featured' => true],
    ];
@endphp

<x-admin-layout>
    <style>
        /* Font, ukuran teks, tombol, dan radius semuanya dari theme.css (var(--fs-*), var(--radius*), .btn) */
        .dash {
            --d-soft: color-mix(in srgb, var(--color-primary) 7%, white);
            --d-warn: #e8a317;
            width: 100%;
            max-width: 80rem;
            margin-inline: auto;
            padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem;
        }
        .dash > * + * { margin-top: 1.5rem; }

        /* ---------- Kepala halaman ---------- */
        .dash-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        .dash-head h2 { font-size: var(--fs-h2); }
        .dash-head p { margin-top: 0.15rem; font-size: var(--fs-small); }
        .dash-head-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }

        /* ---------- Kartu angka ---------- */
        .dash-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .dash-stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.5rem;
            color: inherit;
            text-decoration: none;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        a.dash-stat:hover { transform: translateY(-2px); box-shadow: 0 18px 32px -22px rgba(74, 31, 143, 0.55); }
        .dash-stat-value { font-family: var(--font-heading); font-size: var(--fs-h1); font-weight: 600; line-height: 1.1; color: var(--color-text); }
        .dash-stat-label { margin-top: 0.15rem; font-size: var(--fs-small); font-weight: 500; }
        .dash-stat-note { margin-top: 0.1rem; font-size: var(--fs-caption); }
        .dash-stat-icon { flex: none; width: 2.25rem; height: 2.25rem; color: var(--color-primary); opacity: 0.85; }
        .dash-stat.is-featured { color: #fff; background: var(--gradient-brand); border-color: transparent; box-shadow: 0 18px 34px -20px rgba(139, 47, 208, 0.8); }
        .dash-stat.is-featured .dash-stat-value,
        .dash-stat.is-featured .dash-stat-label,
        .dash-stat.is-featured .dash-stat-note { color: #fff; }
        .dash-stat.is-featured .dash-stat-note { opacity: 0.85; }
        .dash-stat.is-featured .dash-stat-icon { color: #fff; opacity: 1; }

        /* ---------- Panel ---------- */
        .dash-grid { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr); gap: 1.5rem; align-items: start; }
        .dash-panel { overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .dash-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; }
        .dash-panel-head h3 { font-size: var(--fs-h4); }

        /* ---------- Tabel proyek ---------- */
        .dash-table { width: 100%; border-collapse: collapse; }
        .dash-table th {
            padding: 0.75rem 1.25rem;
            font-size: var(--fs-small);
            font-weight: 600;
            text-align: left;
            color: var(--color-text);
            border-block: 1px solid var(--color-border);
        }
        .dash-table td { padding: 0.9rem 1.25rem; font-size: var(--fs-small); color: var(--color-muted); }
        .dash-table tbody tr + tr td { border-top: 1px solid var(--color-border); }
        .dash-table tbody tr:hover td { background: var(--d-soft); }
        .dash-table .dash-project-title { font-weight: 600; color: var(--color-text); }

        .dash-status { display: inline-flex; align-items: center; gap: 0.5rem; }
        .dash-status::before { content: ""; width: 0.55rem; height: 0.55rem; border-radius: 50%; background: var(--color-muted); }
        .dash-status.is-live::before { background: var(--color-success); }
        .dash-status.is-draft::before { background: var(--d-warn); }

        /* ---------- Daftar klien ---------- */
        .dash-person { display: flex; align-items: center; gap: 0.8rem; padding: 0.8rem 1.25rem; border-top: 1px solid var(--color-border); }
        .dash-person-avatar {
            flex: none;
            display: grid;
            place-items: center;
            width: 2.5rem;
            height: 2.5rem;
            overflow: hidden;
            background: #241a4a;
            color: #fff;
            border-radius: 50%;
            font-family: var(--font-heading);
            font-size: var(--fs-small);
            font-weight: 600;
        }
        .dash-person-avatar img { width: 100%; height: 100%; padding: 0.4rem; object-fit: contain; }
        .dash-person-text { flex: 1; min-width: 0; }
        .dash-person-name { overflow: hidden; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); text-overflow: ellipsis; white-space: nowrap; }
        .dash-person-sub { overflow: hidden; font-size: var(--fs-caption); text-overflow: ellipsis; white-space: nowrap; }
        .dash-person-actions { display: flex; gap: 0.15rem; }
        .dash-iconlink { display: grid; place-items: center; width: 2rem; height: 2rem; color: var(--color-primary); border-radius: var(--radius); transition: background 0.2s; }
        .dash-iconlink:hover { background: var(--d-soft); }
        .dash-iconlink svg { width: 1.1rem; height: 1.1rem; }

        .dash-empty { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 2rem 1.25rem 2.25rem; text-align: center; border-top: 1px solid var(--color-border); }

        /* ---------- Responsif ---------- */
        @media (max-width: 1100px) {
            .dash-stats { grid-template-columns: repeat(2, 1fr); }
            .dash-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .dash-head-actions { width: 100%; }
            .dash-head-actions .btn { flex: 1; }

            /* Tabel berubah jadi daftar kartu */
            .dash-table thead { display: none; }
            .dash-table, .dash-table tbody, .dash-table tr, .dash-table td { display: block; width: 100%; }
            .dash-table tr { padding: 0.9rem 1.25rem; border-top: 1px solid var(--color-border); }
            .dash-table tbody tr:first-child { border-top: 0; }
            .dash-table td { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.2rem 0; border: 0 !important; }
            .dash-table tbody tr:hover td { background: transparent; }
            .dash-table td::before { content: attr(data-label); font-size: var(--fs-caption); letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-muted); }
            .dash-table td:first-child { margin-bottom: 0.2rem; }
            .dash-table td:first-child::before { display: none; }
        }
        @media (max-width: 480px) {
            .dash-stats { grid-template-columns: 1fr; }
        }
    </style>

    <div class="dash">

        {{-- ===== KEPALA ===== --}}
        <div class="dash-head">
            <div>
                <h2>Halo, {{ $firstName }}</h2>
                <p>{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>

            <div class="dash-head-actions">
                @if (Route::has('admin.projects.create'))
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                        <x-admin.icon name="plus" />
                        Tambah proyek
                    </a>
                @endif
                <a href="{{ $siteUrl }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                    Buka website
                    <x-admin.icon name="external" />
                </a>
            </div>
        </div>

        {{-- ===== KARTU ANGKA ===== --}}
        <section class="dash-stats" aria-label="Ringkasan konten">
            @foreach ($stats as $stat)
                @php
                    $linked = Route::has($stat['route']);
                    $tag    = $linked ? 'a' : 'div';
                @endphp

                <{{ $tag }} class="dash-stat {{ ($stat['featured'] ?? false) ? 'is-featured' : '' }}"
                    @if ($linked) href="{{ route($stat['route']) }}" @endif>
                    <div>
                        <p class="dash-stat-value">{{ $stat['total'] }}</p>
                        <p class="dash-stat-label">{{ $stat['label'] }}</p>
                        <p class="dash-stat-note">{{ $linked ? $stat['note'] : 'Segera hadir' }}</p>
                    </div>
                    <x-admin.icon :name="$stat['icon']" class="dash-stat-icon" />
                </{{ $tag }}>
            @endforeach
        </section>

        <div class="dash-grid">

            {{-- ===== PROYEK TERBARU ===== --}}
            <section class="dash-panel">
                <div class="dash-panel-head">
                    <h3>Proyek Terbaru</h3>
                    @if (Route::has('admin.projects.index'))
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-primary btn-sm">
                            Lihat semua
                            <x-admin.icon name="arrow" />
                        </a>
                    @endif
                </div>

                @if ($projects->isEmpty())
                    <div class="dash-empty">
                        <p>Belum ada proyek.</p>
                        @if (Route::has('admin.projects.create'))
                            <a href="{{ route('admin.projects.create') }}" class="btn btn-outline btn-sm">
                                <x-admin.icon name="plus" />
                                Tambah proyek
                            </a>
                        @endif
                    </div>
                @else
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Judul Proyek</th>
                                <th>Tag</th>
                                <th>Tahun</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <td class="dash-project-title" data-label="Judul">{{ $project->getTranslation('title', 'id') }}</td>
                                    <td data-label="Tag">{{ $project->getTranslation('tag', 'id') ?: '-' }}</td>
                                    <td data-label="Tahun">{{ $project->release_year ?: '-' }}</td>
                                    <td data-label="Status">
                                        <span class="dash-status {{ $project->is_published ? 'is-live' : 'is-draft' }}">
                                            {{ $project->is_published ? 'Tayang' : 'Draf' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            {{-- ===== KLIEN TERBARU ===== --}}
            <section class="dash-panel">
                <div class="dash-panel-head">
                    <h3>Klien Terbaru</h3>
                    @if (Route::has('admin.clients.index'))
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-primary btn-sm">
                            Lihat semua
                            <x-admin.icon name="arrow" />
                        </a>
                    @endif
                </div>

                @forelse ($clients as $client)
                    <div class="dash-person">
                        <span class="dash-person-avatar">
                            @if ($client->logo)
                                <img src="{{ asset('storage/'.$client->logo) }}" alt="{{ $client->name }}">
                            @else
                                {{ mb_strtoupper(mb_substr($client->name, 0, 1)) }}
                            @endif
                        </span>

                        <div class="dash-person-text">
                            <p class="dash-person-name">{{ $client->name }}</p>
                            <p class="dash-person-sub">
                                {{ $client->website_url ? parse_url($client->website_url, PHP_URL_HOST) : 'Tanpa website' }}
                            </p>
                        </div>

                        <div class="dash-person-actions">
                            @if (Route::has('admin.clients.edit'))
                                <a href="{{ route('admin.clients.edit', $client) }}" class="dash-iconlink" aria-label="Ubah {{ $client->name }}">
                                    <x-admin.icon name="edit" />
                                </a>
                            @endif
                            @if ($client->website_url)
                                <a href="{{ $client->website_url }}" target="_blank" rel="noopener" class="dash-iconlink" aria-label="Buka website {{ $client->name }}">
                                    <x-admin.icon name="link" />
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="dash-empty">
                        <p>Belum ada klien.</p>
                        @if (Route::has('admin.clients.create'))
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-outline btn-sm">
                                <x-admin.icon name="plus" />
                                Tambah klien
                            </a>
                        @endif
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-admin-layout>