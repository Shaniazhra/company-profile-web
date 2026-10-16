@props(['site' => null])

@php
    $brand = $site?->site_name ?: config('app.name');

    // Menu per kategori. Item otomatis disembunyikan kalau route modulnya belum ada,
    // dan kategori yang kosong ikut disembunyikan.
    $groups = [
        'Utama' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.index', 'match' => ['admin.index']],
        ],
        'Home page' => [
            ['label' => 'Services', 'icon' => 'services', 'route' => 'admin.services.index', 'match' => ['admin.services.*']],
            ['label' => 'Projects', 'icon' => 'projects', 'route' => 'admin.projects.index', 'match' => ['admin.projects.*']],
            ['label' => 'Process', 'icon' => 'projects', 'route' => 'admin.process.index', 'match' => ['admin.process.*']],
            ['label' => 'Highlights', 'icon' => 'services', 'route' => 'admin.highlights.index', 'match' => ['admin.highlights.*']],
            ['label' => 'Focus screenshots', 'icon' => 'projects', 'route' => 'admin.focus.index', 'match' => ['admin.focus.*']],
            ['label' => 'Clients', 'icon' => 'clients', 'route' => 'admin.clients.index', 'match' => ['admin.clients.*']],
            ['label' => 'FAQ', 'icon' => 'services', 'route' => 'admin.faqs.index', 'match' => ['admin.faqs.*']],
        ],
        'Company' => [
            ['label' => 'Team', 'icon' => 'team', 'route' => 'admin.team.index', 'match' => ['admin.team.*']],
            ['label' => 'Contacts', 'icon' => 'user', 'route' => 'admin.contacts.index', 'match' => ['admin.contacts.*']],
        ],
        'Inbox' => [
            ['label' => 'Inquiries', 'icon' => 'clients', 'route' => 'admin.inquiries.index', 'match' => ['admin.inquiries.*']],
        ],
        'Settings' => [
            ['label' => 'Site Settings', 'icon' => 'settings', 'route' => 'admin.site.edit', 'match' => ['admin.site.*']],
        ],
    ];

    $groups = collect($groups)
        ->map(fn ($items) => collect($items)->filter(fn ($item) => Route::has($item['route']))->all())
        ->filter(fn ($items) => count($items) > 0);
@endphp

<div class="adm-brand">
    <x-brand-logo :site="$site" :brand="$brand" size="md" sub="Panel Admin" />

    <button type="button" class="adm-close" @click="open = false" aria-label="Close menu">
        <x-admin.icon name="close" class="h-5 w-5" />
    </button>
</div>

<nav class="adm-nav" aria-label="Menu admin">
    @foreach ($groups as $title => $items)
        <p class="adm-nav-label {{ $loop->first ? '' : 'mt-5' }}">{{ $title }}</p>

        @foreach ($items as $item)
            @php $active = request()->routeIs(...$item['match']); @endphp
            <a href="{{ route($item['route']) }}" class="adm-link {{ $active ? 'is-active' : '' }}"
               @if ($active) aria-current="page" @endif>
                <x-admin.icon :name="$item['icon']" class="h-5 w-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    @endforeach
</nav>

<div class="adm-foot">

    <a href="{{ route('profile.edit') }}" class="adm-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
        <x-admin.icon name="user" class="h-5 w-5" />
        <span>Profil</span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="adm-link">
            <x-admin.icon name="logout" class="h-5 w-5" />
            <span>Log out</span>
        </button>
    </form>
</div>