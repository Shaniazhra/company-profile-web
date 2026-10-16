<div>
    <x-input-label for="title" value="Stage name" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title)" required />
    <x-input-error :messages="$errors->get('title')" class="mt-1" />
</div>
<div>
    <x-input-label for="summary" value="Short summary (optional)" />
    <x-text-input id="summary" name="summary" type="text" class="mt-1 block w-full" :value="old('summary', $item->summary)" />
    <x-input-error :messages="$errors->get('summary')" class="mt-1" />
</div>
<div>
    <x-input-label for="details" value="Details (optional)" />
    <textarea id="details" name="details" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('details', $item->details) }}</textarea>
    <p class="mt-1 text-xs text-gray-500">Shown on the Process page under the summary.</p>
    <x-input-error :messages="$errors->get('details')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.process.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>