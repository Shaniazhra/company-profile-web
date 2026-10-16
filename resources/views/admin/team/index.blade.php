<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Team</h1>
                    <p class="text-sm text-gray-500">People shown in the team section on the home page and on the Team page.</p>
                </div>
                <a href="{{ route('admin.team.create') }}" class="btn btn-primary">+ Add Team member</a>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($items as $item)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                        <div class="flex h-14 w-14 flex-none items-center justify-center overflow-hidden rounded-md bg-gray-200">
                            @if ($item->photo)
                                <img src="{{ asset('storage/'.$item->photo) }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-xs text-gray-500">No photo</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-800">{{ $item->name }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $item->role }}</p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $item->is_active ? 'Shown' : 'Hidden' }}
                        </span>

                        <x-admin.row-actions
                            :move="route('admin.team.move', $item)"
                            :edit="route('admin.team.edit', $item)"
                            :delete="route('admin.team.destroy', $item)"
                            :first="$loop->first" :last="$loop->last"
                            confirm="Delete this item? This cannot be undone." />
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">Nothing here yet. Click "Add Team member" to get started.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>