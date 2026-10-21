@php
    // English only: the text is saved in the "en" language of the column.
    $field   = $t['field'];
    $current = method_exists($site, 'isTranslatableAttribute') && $site->isTranslatableAttribute($field)
        ? ($site->getTranslation($field, 'en', false) ?? '')
        : (string) ($site->{$field} ?? '');
@endphp

<section id="sec-{{ $field }}" class="site-card">
    <div class="site-card-head">
        <h3>{{ $t['title'] }}</h3>
        <p>{{ $t['desc'] }}</p>
    </div>

    <div class="site-card-body">
        <textarea id="f-{{ $field }}" name="{{ $field }}" form="{{ $form }}" rows="{{ $t['rows'] }}"
                  class="site-textarea" aria-label="{{ $t['title'] }}">{{ old($field, $current) }}</textarea>
        @error($field)
            <p class="img-up-error">{{ $message }}</p>
        @enderror
    </div>
</section>