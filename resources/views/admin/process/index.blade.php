<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Process stages</h1>
                    <p class="text-sm text-gray-500">The steps in the work process, shown on the home page and the Process page.</p>
                </div>
                <a href="{{ route('admin.process.create') }}" class="btn btn-primary">+ Add Stage</a>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($items as $item)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-800">{{ $item->title }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $item->summary ?: 'No summary yet' }}</p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $item->is_active ? 'Shown' : 'Hidden' }}
                        </span>

                        <x-admin.row-actions
                            :move="route('admin.process.move', $item)"
                            :edit="route('admin.process.edit', $item)"
                            :delete="route('admin.process.destroy', $item)"
                            :first="$loop->first" :last="$loop->last"
                            confirm="Delete this item? This cannot be undone." />
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">Nothing here yet. Click "Add Stage" to get started.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>