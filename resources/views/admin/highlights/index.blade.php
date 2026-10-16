<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Highlights</h1>
                    <p class="text-sm text-gray-500">"Why choose us" reasons and the result lines under the process steps on the home page.</p>
                </div>
                <a href="{{ route('admin.highlights.create') }}" class="btn btn-primary">+ Add Highlight</a>
            </div>

            <x-admin.flash />

            @foreach (['reason' => 'Why choose us', 'result' => 'Results'] as $key => $heading)
                @php $rows = $groups->get($key, collect()); @endphp
                <h2 class="mb-2 mt-6 px-4 sm:px-0 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $heading }}</h2>
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    @forelse ($rows as $item)
                        <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                            <div class="min-w-0 flex-1">
                                @if ($item->title)<p class="truncate font-medium text-gray-800">{{ $item->title }}</p>@endif
                                <p class="truncate text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->text, 110) }}</p>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $item->is_active ? 'Shown' : 'Hidden' }}
                            </span>

                            <x-admin.row-actions
                                :move="route('admin.highlights.move', $item)"
                                :edit="route('admin.highlights.edit', $item)"
                                :delete="route('admin.highlights.destroy', $item)"
                                :first="$loop->first" :last="$loop->last"
                                confirm="Delete this highlight? This cannot be undone." />
                        </div>
                    @empty
                        <p class="p-6 text-center text-gray-500">Nothing here yet. The home page falls back to its default text.</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>