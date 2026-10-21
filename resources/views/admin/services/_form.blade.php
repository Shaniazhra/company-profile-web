<x-admin.translatable name="title" label="Service Title" :model="$service" />
<x-admin.translatable name="summary" label="Summary (1-2 sentences, shown on the home page card)" :model="$service" :rows="2" />

<x-admin.image-field name="image" label="Screenshot" :path="$service->image"
    help="Shown on this service's card on the home page. 16:10 landscape fits best, max 2 MB." />

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