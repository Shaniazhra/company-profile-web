<div>
    <x-input-label for="title" value="Title" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title)" required />
    <x-input-error :messages="$errors->get('title')" class="mt-1" />
</div>

<x-admin.image-field name="image" label="Screenshot" :path="$item->image" help="PNG, JPG, or WebP. A 16:10 image works best. Max 2 MB." />

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.focus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>