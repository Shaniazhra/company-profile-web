<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 px-4 sm:px-0">
                <h1 class="text-xl font-semibold text-gray-800">Inquiries</h1>
                <p class="text-sm text-gray-500">Messages sent from the Contact page.</p>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($items as $item)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                        <span class="h-2.5 w-2.5 flex-none rounded-full {{ $item->is_read ? 'bg-gray-300' : 'bg-indigo-600' }}" title="{{ $item->is_read ? 'Read' : 'Unread' }}"></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-gray-800 {{ $item->is_read ? '' : 'font-semibold' }}">{{ $item->name }} <span class="font-normal text-gray-500">({{ $item->contact }})</span></p>
                            <p class="truncate text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->message, 110) }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $item->created_at->format('d M Y, H:i') }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.inquiries.show', $item) }}" class="btn btn-outline btn-sm">View</a>
                            <form method="POST" action="{{ route('admin.inquiries.read', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">{{ $item->is_read ? 'Mark unread' : 'Mark read' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.inquiries.destroy', $item) }}" onsubmit="return confirm('Delete this message? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">No messages yet.</p>
                @endforelse
            </div>

            <div class="mt-4 px-4 sm:px-0">{{ $items->links() }}</div>
        </div>
    </div>
</x-admin-layout>