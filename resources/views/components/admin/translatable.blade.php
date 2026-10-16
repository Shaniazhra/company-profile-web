@props(['name', 'label', 'model' => null, 'rows' => 1])

<fieldset class="space-y-3">
    <legend class="font-semibold text-gray-700">{{ $label }}</legend>

    @foreach (\App\Support\AdminForm::LOCALES as $code => $language)
        <div>
            <x-input-label :for="$name.'_'.$code" :value="$language" />
            <textarea id="{{ $name }}_{{ $code }}" name="{{ $name }}[{{ $code }}]" rows="{{ $rows }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old("$name.$code", $model?->getTranslation($name, $code, false)) }}</textarea>
            <x-input-error :messages="$errors->get($name.'.'.$code)" class="mt-1" />
        </div>
    @endforeach
</fieldset>