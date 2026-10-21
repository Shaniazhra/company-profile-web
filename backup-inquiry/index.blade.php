<x-admin-layout>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-gray-800">Inquiries</h1>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('admin.inquiries.index') }}"
                   class="{{ $filter === 'all' ? 'font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.inquiries.index', ['filter' => 'unread']) }}"
                   class="{{ $filter === 'unread' ? 'font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    Unread ({{ $counts['unread'] }})
                </a>
            </div>
        </div>

        @if (session('status'))
            <p class="rounded-md bg-green-50 px-4 py-2 text-sm text-green-700">{{ session('status') }}</p>
        @endif

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">From</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Message</th>
                        <th class="px-4 py-3">Received</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($inquiries as $i)
                        <tr class="{{ $i->is_read ? 'text-gray-600' : 'bg-indigo-50/40 font-medium text-gray-900' }}">
                            <td class="px-4 py-3">
                                @unless ($i->is_read)
                                    <span class="mr-2 inline-block h-2 w-2 rounded-full bg-indigo-600" title="Unread"></span>
                                @endunless
                                {{ $i->name }}
                            </td>
                            <td class="px-4 py-3">{{ $i->contact }}</td>
                            <td class="max-w-xs px-4 py-3">{{ \Illuminate\Support\Str::limit($i->message, 70) }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $i->created_at->format('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a href="{{ route('admin.inquiries.show', $i) }}" class="text-indigo-600 hover:text-indigo-800">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $inquiries->links() }}
    </div>
</x-admin-layout>