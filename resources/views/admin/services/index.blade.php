<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Services</h1>
                    <p class="text-sm text-gray-500">Animation skills shown to potential clients. The order here matches the order on the website.</p>
                </div>
                <a href="{{ route('admin.services.create') }}"
                   class="btn btn-primary">
                    + Add Service
                </a>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($services as $service)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                        <div class="flex h-12 w-12 flex-none items-center justify-center rounded-md bg-indigo-50 text-indigo-600">
                            {{ \App\Support\Icons::svg($service->icon, 'h-6 w-6') }}
                        </div>

                        @if ($service->image)
                            <img src="{{ asset('storage/'.$service->image) }}" alt="" class="h-12 w-20 flex-none rounded-md object-cover">
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-800">{{ $service->getTranslation('title', 'en') }}</p>
                            <p class="truncate text-sm text-gray-500">{{ $service->getTranslation('summary', 'en', false) ?: 'No summary yet' }}</p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $service->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $service->is_active ? 'Tampil' : 'Disembunyikan' }}
                        </span>

                        <x-admin.row-actions
                            :move="route('admin.services.move', $service)"
                            :edit="route('admin.services.edit', $service)"
                            :delete="route('admin.services.destroy', $service)"
                            :first="$loop->first" :last="$loop->last"
                            confirm="Delete this service? Its image will be deleted too." />
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">No services yet. Click "Add Service" to get started.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>