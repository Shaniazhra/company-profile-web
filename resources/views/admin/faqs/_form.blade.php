<div>
    <x-input-label for="question" value="Question" />
    <x-text-input id="question" name="question" type="text" class="mt-1 block w-full" :value="old('question', $item->question)" required />
    <x-input-error :messages="$errors->get('question')" class="mt-1" />
</div>
<div>
    <x-input-label for="answer" value="Answer" />
    <textarea id="answer" name="answer" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('answer', $item->answer) }}</textarea>
    <p class="mt-1 text-xs text-gray-500">Plain text. Line breaks are kept.</p>
    <x-input-error :messages="$errors->get('answer')" class="mt-1" />
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
           @checked(old('is_active', $item->exists ? $item->is_active : true))>
    Show on website
</label>

<div class="flex items-center gap-3">
    <x-admin.button>Save</x-admin.button>
    <a href="{{ route('admin.faqs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
</div>