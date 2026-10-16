<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Focus screenshots</h1>
                    <p class="text-sm text-gray-500">Screenshots beside the reasons in "Why choose us". The column is hidden if no image is uploaded.</p>
                </div>
                <a href="{{ route('admin.focus.create') }}" class="btn btn-primary">+ Add Screenshot</a>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($items as $item)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                        <div class="flex h-14 w-24 flex-none items-center justify-center overflow-hidden rounded-md bg-gray-200">
                            @if ($item->image)
                                <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-xs text-gray-500">No image</span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-800">{{ $item->title }}</p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $item->is_active ? 'Shown' : 'Hidden' }}
                        </span>

                        <x-admin.row-actions
                            :move="route('admin.focus.move', $item)"
                            :edit="route('admin.focus.edit', $item)"
                            :delete="route('admin.focus.destroy', $item)"
                            :first="$loop->first" :last="$loop->last"
                            confirm="Delete this screenshot? The image will be deleted too." />
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">No screenshots yet. Click "Add Screenshot" to get started.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>