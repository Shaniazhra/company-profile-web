@props(['name', 'label', 'path' => null, 'help' => null, 'dark' => false])

<fieldset class="space-y-3">
    <legend class="font-semibold text-gray-700">{{ $label }}</legend>

    @if ($path)
        <div class="flex items-center gap-4 rounded-md p-4 {{ $dark ? 'bg-gray-900' : 'bg-gray-100' }}">
            <img src="{{ asset('storage/'.$path) }}" alt="{{ $label }}" class="h-24 w-auto object-contain">
            <label class="flex items-center gap-2 text-sm {{ $dark ? 'text-gray-200' : 'text-gray-700' }}">
                <input type="checkbox" name="remove_{{ $name }}" value="1" class="rounded border-gray-300">
                Hapus gambar ini
            </label>
        </div>
    @endif

    <div>
        <input id="{{ $name }}" type="file" name="{{ $name }}" accept="image/png,image/jpeg,image/webp"
               class="mt-1 block w-full text-sm text-gray-700">
        <p class="mt-1 text-xs text-gray-500">{{ $help ?? 'PNG, JPG, atau WebP, maks 2 MB.' }}</p>
        <x-input-error :messages="$errors->get($name)" class="mt-1" />
    </div>
</fieldset>