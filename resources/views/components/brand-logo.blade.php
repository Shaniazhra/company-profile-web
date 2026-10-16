@props([
    'site'  => null,
    'brand' => null,    // opsional, kalau nama brand mau di-override
    'size'  => 'md',    // sm | md | lg
    'name'  => true,    // false = logo saja tanpa nama
    'sub'   => null,    // teks kecil di bawah nama, mis. "Panel Admin"
])

@php
    $brand = $brand ?: ($site?->site_name ?: config('app.name'));
    $logo  = ! empty($site?->logo) ? asset('storage/'.$site->logo) : null;
@endphp

@once
    <style>
        /* Satu-satunya tempat gaya logo. Font & ukuran teks dari token theme.css. */
        .brand-logo { --logo-h: 2.25rem; --logo-fs: var(--fs-h4, 1.125rem); display: inline-flex; align-items: center; gap: 0.85rem; min-width: 0; color: inherit; line-height: 1.2; }
        .brand-logo.brand-logo--sm { --logo-h: 1.75rem; --logo-fs: var(--fs-small, 0.875rem); gap: 0.65rem; }
        .brand-logo.brand-logo--md { --logo-h: 2.25rem; --logo-fs: var(--fs-h4, 1.125rem); }
        .brand-logo.brand-logo--lg { --logo-h: 3rem;    --logo-fs: var(--fs-h3, 1.25rem); gap: 1rem; }

        .brand-logo .brand-logo-img { display: block; flex: none; width: auto; height: var(--logo-h); max-width: calc(var(--logo-h) * 3); object-fit: contain; }
        .brand-logo .brand-logo-text { min-width: 0; font-family: var(--font-heading, inherit); font-size: var(--logo-fs); font-weight: 700; letter-spacing: -0.01em; }
        .brand-logo .brand-logo-text small { display: block; margin-top: 0.1rem; font-family: var(--font-body, inherit); font-size: var(--fs-caption, 0.75rem); font-weight: 500; letter-spacing: 0; opacity: 0.7; }
    </style>
@endonce

<span {{ $attributes->class(['brand-logo', 'brand-logo--'.$size]) }}>
    @if ($logo)
        <img class="brand-logo-img" src="{{ $logo }}" alt="{{ $name ? '' : $brand }}">
    @endif

    @if ($name || ! $logo)
        <span class="brand-logo-text">
            {{ $brand }}
            @if ($sub)<small>{{ $sub }}</small>@endif
        </span>
    @endif
</span>