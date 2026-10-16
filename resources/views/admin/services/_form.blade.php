<x-admin.translatable name="title" label="Service Title" :model="$service" />
<x-admin.translatable name="summary" label="Ringkasan (1-2 kalimat, tampil di kartu)" :model="$service" :rows="2" />
<x-admin.translatable name="description" label="Full description" :model="$service" :rows="5" />

<fieldset>
    <legend class="font-semibold text-gray-700">Ikon</legend>
    <div class="mt-2 grid grid-cols-5 gap-2 sm:grid-cols-10">
        @foreach (\App\Support\Icons::all() as $key => [$iconLabel])
            <label class="cursor-pointer" title="{{ $iconLabel }}">
                <input type="radio" name="icon" value="{{ $key }}" class="peer sr-only"
                       @checked(old('icon', $service->icon ?? 'video') === $key)>
                <span class="flex h-12 items-center justify-center rounded-md border border-gray-300 text-gray-500 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-400">
                    {{ \App\Support\Icons::svg($key, 'h-6 w-6') }}
                </span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('icon')" class="mt-1" />
</fieldset>

<x-admin.image-field name="image" label="Sample result image" :path="$service->image"
    help="A preview of the animation result for this service (16:9 landscape fits best), max 2 MB." />

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $service->exists ? $service->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.services.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>