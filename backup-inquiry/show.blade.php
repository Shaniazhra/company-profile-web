<x-admin-layout>
    @php
        $isEmail = filter_var($inquiry->contact, FILTER_VALIDATE_EMAIL);
        $href    = $isEmail ? 'mailto:'.$inquiry->contact : 'tel:'.preg_replace('/[^\d+]/', '', $inquiry->contact);
    @endphp

    <div class="mx-auto max-w-3xl space-y-4">
        <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to inquiries</a>

        @if (session('status'))
            <p class="rounded-md bg-green-50 px-4 py-2 text-sm text-green-700">{{ session('status') }}</p>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <dl class="grid gap-4 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Name</dt>
                    <dd class="mt-1 text-gray-900">{{ $inquiry->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Contact</dt>
                    <dd class="mt-1">
                        <a href="{{ $href }}" class="text-indigo-600 hover:text-indigo-800">{{ $inquiry->contact }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Received</dt>
                    <dd class="mt-1 text-gray-900">{{ $inquiry->created_at->format('d M Y, H:i') }}</dd>
                </div>
            </dl>

            <hr class="my-5 border-gray-200">

            <p class="whitespace-pre-line text-sm leading-relaxed text-gray-800">{{ $inquiry->message }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ $href }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                {{ $isEmail ? 'Reply by email' : 'Call' }}
            </a>

            <form method="POST" action="{{ route('admin.inquiries.toggle', $inquiry) }}">
                @csrf @method('PATCH')
                <button class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Mark as {{ $inquiry->is_read ? 'unread' : 'read' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                  onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button class="rounded-md px-4 py-2 text-sm text-red-600 hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>
</x-admin-layout>