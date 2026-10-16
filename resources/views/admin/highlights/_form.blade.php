<div>
    <x-input-label for="group" value="Where it appears" />
    <select id="group" name="group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach (['reason' => 'Why choose us (title, text, icon)', 'result' => 'Results (one line under the process steps)'] as $value => $label)
            <option value="{{ $value }}" @selected(old('group', $item->group ?: request('group', 'reason')) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('group')" class="mt-1" />
</div>

<div>
    <x-input-label for="title" value="Title (needed for Why choose us)" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title)" />
    <x-input-error :messages="$errors->get('title')" class="mt-1" />
</div>

<div>
    <x-input-label for="text" value="Text" />
    <textarea id="text" name="text" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('text', $item->text) }}</textarea>
    <x-input-error :messages="$errors->get('text')" class="mt-1" />
</div>

<div>
    <x-input-label for="icon" value="Icon (Why choose us only)" />
    <select id="icon" name="icon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach (\App\Models\Highlight::ICONS as $key => [$label])
            <option value="{{ $key }}" @selected(old('icon', $item->icon ?: 'star') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('icon')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.highlights.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>