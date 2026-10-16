<x-admin-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Klien</h1>
                    <p class="text-sm text-gray-500">Logo klien yang tampil di bagian "Dipercaya oleh".</p>
                </div>
                <a href="{{ route('admin.clients.create') }}"
                   class="btn btn-primary">
                    + Tambah Klien
                </a>
            </div>

            <x-admin.flash />

            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                @forelse ($clients as $client)
                    <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-4 last:border-b-0">
                        <div class="flex h-14 w-28 flex-none items-center justify-center rounded-md bg-gray-700 p-2">
                            @if ($client->logo)
                                <img src="{{ asset('storage/'.$client->logo) }}" alt="{{ $client->name }}" class="max-h-full max-w-full object-contain">
                            @else
                                <span class="text-xs text-gray-300">Tanpa logo</span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-800">{{ $client->name }}</p>
                            @if ($client->website_url)
                                <a href="{{ $client->website_url }}" target="_blank" rel="noopener" class="truncate text-sm text-indigo-600 hover:underline">{{ $client->website_url }}</a>
                            @endif
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $client->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $client->is_active ? 'Tampil' : 'Disembunyikan' }}
                        </span>

                        <x-admin.row-actions
                            :move="route('admin.clients.move', $client)"
                            :edit="route('admin.clients.edit', $client)"
                            :delete="route('admin.clients.destroy', $client)"
                            :first="$loop->first" :last="$loop->last"
                            confirm="Hapus klien ini? Logonya juga ikut terhapus." />
                    </div>
                @empty
                    <p class="p-8 text-center text-gray-500">Belum ada klien. Klik "Tambah Klien" untuk memulai.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>