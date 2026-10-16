<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $item->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
<div>
    <x-input-label for="role" value="Role" />
    <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="old('role', $item->role)" required />
    <x-input-error :messages="$errors->get('role')" class="mt-1" />
</div>
<x-admin.image-field name="photo" label="Photo" :path="$item->photo" help="A PNG with a transparent background looks best. Max 2 MB." />

<div>
    <x-input-label for="credits" value="Credits (optional)" />
    <textarea id="credits" name="credits" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('credits', $item->credits) }}</textarea>
    <p class="mt-1 text-xs text-gray-500">One credit per line.</p>
    <x-input-error :messages="$errors->get('credits')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.team.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>