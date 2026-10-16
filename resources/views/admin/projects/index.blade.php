<x-admin-layout>
    <style>
        .pa { width: 100%; max-width: 80rem; margin-inline: auto; padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 2rem) 3rem; }
        .pa > * + * { margin-top: 1.25rem; }
        .pa-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        .pa-head h2 { font-size: var(--fs-h2); }
        .pa-head p { margin-top: 0.15rem; font-size: var(--fs-small); }

        .pa-flash { padding: 0.8rem 1.1rem; font-size: var(--fs-small); font-weight: 500; color: #14633f; background: #e6f7ee; border: 1px solid #c4ebd5; border-radius: var(--radius); }

        .pa-card { overflow: hidden; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 12px 28px -24px rgba(36, 26, 74, 0.45); }
        .pa-scroll { overflow-x: auto; }
        .pa-table { width: 100%; min-width: 44rem; border-collapse: collapse; }
        .pa-table th { padding: 0.75rem 1.25rem; font-size: var(--fs-small); font-weight: 600; text-align: left; color: var(--color-text); border-bottom: 1px solid var(--color-border); }
        .pa-table td { padding: 0.85rem 1.25rem; font-size: var(--fs-small); color: var(--color-muted); vertical-align: middle; }
        .pa-table tbody tr + tr td { border-top: 1px solid var(--color-border); }
        .pa-table tbody tr:hover td { background: color-mix(in srgb, var(--color-primary) 6%, white); }

        .pa-title { display: flex; align-items: center; gap: 0.85rem; min-width: 0; }
        .pa-thumb { flex: none; display: grid; place-items: center; width: 4.5rem; aspect-ratio: 16 / 10; overflow: hidden; color: #fff; background: var(--gradient-brand); border-radius: var(--radius); font-family: var(--font-heading); font-size: var(--fs-small); font-weight: 600; }
        .pa-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .pa-name { font-weight: 600; color: var(--color-text); }
        .pa-slug { font-size: var(--fs-caption); }

        .pa-status { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.8rem; font: inherit; font-size: var(--fs-caption); font-weight: 600; color: #8a5a00; background: #fff4d6; border: 0; border-radius: 999px; cursor: pointer; transition: filter 0.2s; }
        .pa-status::before { content: ""; width: 0.5rem; height: 0.5rem; border-radius: 50%; background: currentColor; }
        .pa-status.is-live { color: #14633f; background: #e6f7ee; }
        .pa-status:hover { filter: brightness(0.96); }

        .pa-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .pa-empty { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 3rem 1.25rem; text-align: center; }
        .pa-pager { padding: 0.9rem 1.25rem; border-top: 1px solid var(--color-border); }
    </style>

    <div class="pa">
        <div class="pa-head">
            <div>
                <h2>Projects</h2>
                <p>The project with the lowest order becomes the featured project on the home page.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                <x-admin.icon name="plus" />
                Add project
            </a>
        </div>

        @if (session('status'))
            <div class="pa-flash" role="status">{{ session('status') }}</div>
        @endif

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
                <div class="pa-scroll">
                    <table class="pa-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Tag</th>
                                <th>Tahun</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                @php $title = $project->getTranslation('title', 'en', false); @endphp
                                <tr>
                                    <td>
                                        <div class="pa-title">
                                            <span class="pa-thumb">
                                                @if ($project->image)
                                                    <img src="{{ asset('storage/'.$project->image) }}" alt="" loading="lazy">
                                                @else
                                                    {{ mb_strtoupper(mb_substr($title, 0, 1)) }}
                                                @endif
                                            </span>
                                            <div>
                                                <p class="pa-name">{{ $title }}</p>
                                                @if ($project->slug)<p class="pa-slug">/{{ $project->slug }}</p>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $project->getTranslation('tag', 'en', false) ?: '-' }}</td>
                                    <td>{{ $project->release_year ?: '-' }}</td>
                                    <td>{{ $project->sort_order }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.projects.publish', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="pa-status {{ $project->is_published ? 'is-live' : '' }}"
                                                    title="Click to change status">
                                                {{ $project->is_published ? 'Tayang' : 'Draf' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="pa-actions">
                                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline btn-sm">
                                                <x-admin.icon name="edit" />
                                                Ubah
                                            </a>

                                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete project "'.$title.'"? This action cannot be undone.') }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($projects->hasPages())
                    <div class="pa-pager">{{ $projects->links() }}</div>
                @endif
            @endif
        </section>
    </div>
</x-admin-layout>