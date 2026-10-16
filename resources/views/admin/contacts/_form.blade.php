<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $item->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
<div>
    <x-input-label for="role" value="Role (optional)" />
    <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="old('role', $item->role)" />
    <x-input-error :messages="$errors->get('role')" class="mt-1" />
</div>
<div>
    <x-input-label for="phone" value="Phone (optional)" />
    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $item->phone)" />
    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
</div>
<div>
    <x-input-label for="email" value="Email (optional)" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $item->email)" />
    <x-input-error :messages="$errors->get('email')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.contacts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>