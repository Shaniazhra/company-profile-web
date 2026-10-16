<div>
    <x-input-label for="name" value="Client Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $client->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<x-admin.image-field name="logo" label="Client Logo" :path="$client->logo" :dark="true"
    help="PNG transparan paling baik (logo putih pun aman, latarnya gelap di sini). Maks 2 MB." />

<div>
    <x-input-label for="website_url" value="Website (opsional)" />
    <x-text-input id="website_url" name="website_url" type="url" class="mt-1 block w-full"
                  placeholder="https://contoh.com" :value="old('website_url', $client->website_url)" />
    <x-input-error :messages="$errors->get('website_url')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $client->exists ? $client->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.clients.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>