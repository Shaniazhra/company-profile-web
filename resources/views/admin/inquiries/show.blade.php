<x-admin-layout>

    @php
        $isEmail = str_contains($item->contact, '@');

        // The reply opens with the address, a subject, and a greeting already filled in.
        $siteName = \App\Models\SiteSetting::first()?->site_name ?: config('app.name');
        $subject  = 'Re: Your message to '.$siteName;
        $body     = 'Hi '.$item->name.",\n\n";

        // Gmail in the browser (works without a mail app installed).
        $gmailUrl = $isEmail
            ? 'https://mail.google.com/mail/?view=cm&fs=1&to='.rawurlencode($item->contact).'&su='.rawurlencode($subject).'&body='.rawurlencode($body)
            : null;
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.inquiries.index') }}" class="mb-3 inline-block px-4 sm:px-0 text-sm text-gray-600 hover:text-gray-900">&larr; All messages</a>

            <div class="space-y-4 bg-white p-6 shadow sm:rounded-lg">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">{{ $item->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $item->created_at->format('d M Y, H:i') }}</p>
                </div>

                <p class="text-sm">
                    <span class="text-gray-500">Contact:</span>
                    @if ($isEmail)
                        <a class="text-indigo-600 hover:underline" href="mailto:{{ $item->contact }}">{{ $item->contact }}</a>
                    @else
                        <a class="text-indigo-600 hover:underline" href="tel:{{ preg_replace('/[^\d+]/', '', $item->contact) }}">{{ $item->contact }}</a>
                    @endif
                </p>

                <div class="whitespace-pre-line rounded-md bg-gray-50 p-4 text-gray-800">{{ $item->message }}</div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($gmailUrl)
                        <a href="{{ $gmailUrl }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer">Reply by email</a>
                    @endif

                    <form method="POST" action="{{ route('admin.inquiries.read', $item) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline btn-sm">Mark unread</button>
                    </form>
                    <form method="POST" action="{{ route('admin.inquiries.destroy', $item) }}" onsubmit="return confirm('Delete this message? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>