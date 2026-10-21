@props(['title' => null])

@php
    $site = \App\Models\SiteSetting::first();

    // Judul halaman otomatis dari route, kecuali diberi title="..." manual.
    $title ??= match (true) {
        request()->routeIs('admin.index') => 'Dashboard',
        request()->routeIs('admin.site.*') => 'Site Settings',
        request()->routeIs('admin.services.*') => 'Services',
        request()->routeIs('admin.projects.*') => 'Projects',
        request()->routeIs('admin.clients.*') => 'Clients',
        request()->routeIs('admin.team.*') => 'Team',
        request()->routeIs('admin.process.*') => 'Process',
        request()->routeIs('admin.highlights.*') => 'Highlights',
        request()->routeIs('admin.faqs.*') => 'FAQ',
        request()->routeIs('admin.contacts.*') => 'Contacts',
        request()->routeIs('admin.inquiries.*') => 'Inquiries',
        request()->routeIs('profile.*') => 'Profile',
        default => 'Admin',
    };

    $userName = auth()->user()->name ?? 'Admin';

    // Favicon: pakai kolom favicon kalau ada, kalau tidak pakai logo.
    $faviconPath = $site?->favicon ?: $site?->logo;
    $faviconUrl  = $faviconPath
        ? asset('storage/'.$faviconPath).'?v='.($site?->updated_at?->timestamp ?? 1)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | {{ $site?->site_name ?: config('app.name') }}</title>

    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif

    {{-- Urutan penting: app.css (Tailwind) dulu, theme.css sesudahnya. Font dimuat dari theme.css. --}}
    @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Admin bertema terang: HANYA token warna ini yang ditimpa.
           Font, ukuran teks, radius, dan tombol tetap dari theme.css. */
        body.adm {
            --color-bg: #f4f1fb;
            --color-surface: #ffffff;
            --color-text: #1c1640;
            --color-muted: #6b6585;
            --color-border: #e8e4f3;

            /* Ungu admin dibuat lebih dalam dan tenang (hanya berlaku di admin).
               Ubah tiga baris ini kalau mau menyesuaikan warnanya. */
            --color-primary: #6b2fa0;
            --color-primary-hover: #7d3fb5;
            --gradient-brand: linear-gradient(135deg, #6b2fa0 0%, #443096 100%);
        }
        body.adm::before { content: none; }

        /* Margin bawaan theme.css dinolkan di admin (class margin Tailwind tetap menang) */
        :where(.adm) h1, :where(.adm) h2, :where(.adm) h3, :where(.adm) h4, :where(.adm) p { margin: 0; }

        /* Skala ukuran Tailwind dipetakan ke token theme.css */
        .adm .text-xs { font-size: var(--fs-caption); }
        .adm .text-sm { font-size: var(--fs-small); }
        .adm .text-base { font-size: var(--fs-body); }
        .adm .text-lg { font-size: var(--fs-h4); }
        .adm .text-xl { font-size: var(--fs-h3); }
        .adm .text-2xl { font-size: var(--fs-h2); }
        .adm .text-3xl, .adm .text-4xl { font-size: var(--fs-h1); }

        /* Sudut */
        .adm .rounded-md, .adm .rounded-lg { border-radius: var(--radius); }
        .adm .rounded-xl, .adm .rounded-2xl, .adm .rounded-3xl { border-radius: var(--radius-lg); }
        @media (min-width: 640px) { .adm .sm\:rounded-lg { border-radius: var(--radius-lg); } }

        /* Isian form */
        .adm input:not([type=checkbox]):not([type=radio]):not([type=file]),
        .adm textarea,
        .adm select { font-family: var(--font-body); font-size: var(--fs-body); border-radius: var(--radius); }

        /* Tombol tambahan untuk admin (dasarnya tetap .btn dari theme.css) */
        .btn-danger { background: transparent; color: var(--color-danger); border-color: color-mix(in srgb, var(--color-danger) 45%, white); }
        .btn-danger:hover { background: var(--color-danger); color: #fff; border-color: var(--color-danger); transform: translateY(-1px); }
        .btn-icon { padding-inline: 0.7rem; }
        .btn[disabled] { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

        /* ---------- Kerangka: sidebar + konten ---------- */
        .adm-shell { display: flex; min-height: 100vh; }
        .adm-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .adm-content { flex: 1; min-width: 0; }
        .adm-overlay { position: fixed; inset: 0; z-index: 40; background: rgba(15, 11, 36, 0.5); }

        /* Sidebar */
        .adm-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            width: 16.5rem;
            max-width: 85vw;
            overflow-y: auto;
            color: #fff;
            background: var(--color-primary);
            transform: translateX(-100%);
            transition: transform 0.2s ease;
        }
        .adm-sidebar.is-open { transform: translateX(0); }

        .adm-brand { display: flex; align-items: center; gap: 0.75rem; flex: none; height: 4.25rem; padding: 0 1.25rem; }
        .adm-brand-mark {
            flex: none;
            display: grid;
            place-items: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: var(--radius);
            background: #fff;
            color: var(--color-primary);
            font-family: var(--font-heading);
            font-size: var(--fs-h4);
            font-weight: 700;
        }
        .adm-brand-name {
            min-width: 0;
            overflow: hidden;
            font-family: var(--font-heading);
            font-size: var(--fs-h4);
            font-weight: 600;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .adm-brand-sub { display: block; font-family: var(--font-body); font-size: var(--fs-caption); font-weight: 400; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.75; }
        .adm-close { margin-left: auto; display: grid; place-items: center; width: 2.25rem; height: 2.25rem; color: #fff; background: transparent; border: 0; border-radius: var(--radius); cursor: pointer; }
        .adm-close:hover { background: rgba(255, 255, 255, 0.14); }

        .adm-nav { flex: 1; padding: 1rem 0 1rem 0.75rem; }
        /* Pemisah kategori: warna diset sendiri karena tag <p> di theme.css diwarnai --color-muted
           (abu keunguan) yang menyatu dengan latar sidebar. */
        .adm-nav-label { padding: 0 1rem 0.5rem; font-size: var(--fs-caption); font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #e9d8fa; }
        .adm-foot { flex: none; padding: 0.75rem 0 1rem 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.18); }

        .adm-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            width: 100%;
            margin-bottom: 0.25rem;
            padding: 0.7rem 1rem;
            font-family: var(--font-body);
            font-size: var(--fs-small);
            font-weight: 500;
            color: rgba(255, 255, 255, 0.92);
            text-align: left;
            text-decoration: none;
            background: transparent;
            border: 0;
            border-radius: 999px 0 0 999px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .adm-link svg { flex: none; width: 1.25rem; height: 1.25rem; }
        .adm-link:hover { color: #fff; background: rgba(255, 255, 255, 0.14); }
        .adm-link.is-active { color: var(--color-primary); font-weight: 600; background: var(--color-bg); }

        /* Bar atas */
        .adm-topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            height: 4.25rem;
            padding: 0 clamp(1rem, 3vw, 2rem);
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 8px 24px -22px rgba(36, 26, 74, 0.5);
        }
        .adm-iconbtn { flex: none; display: grid; place-items: center; width: 2.5rem; height: 2.5rem; color: var(--color-text); background: transparent; border: 0; border-radius: var(--radius); cursor: pointer; transition: background 0.2s; }
        .adm-iconbtn:hover { background: color-mix(in srgb, var(--color-primary) 10%, white); }
        .adm-title { margin: 0; min-width: 0; overflow: hidden; font-size: var(--fs-h3); font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .adm-topbar-right { display: flex; align-items: center; gap: 0.75rem; margin-left: auto; }
        .adm-user { display: flex; align-items: center; gap: 0.65rem; }
        .adm-avatar {
            flex: none;
            display: grid;
            place-items: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--gradient-brand);
            color: #fff;
            font-family: var(--font-heading);
            font-size: var(--fs-small);
            font-weight: 600;
        }
        .adm-user-text { line-height: 1.25; }
        .adm-user-name { display: block; font-size: var(--fs-small); font-weight: 600; color: var(--color-text); }
        .adm-user-role { display: block; font-size: var(--fs-caption); color: var(--color-muted); }

        @media (min-width: 1024px) {
            .adm-sidebar { position: sticky; top: 0; flex: none; height: 100vh; transform: none; }
            .adm-sidebar.is-collapsed { display: none; }
            .adm-overlay, .adm-close { display: none !important; }
        }
        @media (max-width: 640px) {
            .adm-user-text, .adm-site-btn { display: none; }
        }
    </style>
</head>
<body class="adm antialiased">
    <div class="adm-shell" x-data="{ open: false, collapsed: false }" @keydown.escape.window="open = false">

        {{-- Lapisan gelap untuk menu geser di HP --}}
        <div class="adm-overlay" x-show="open" x-cloak x-transition.opacity @click="open = false"></div>

        {{-- Sidebar --}}
        <aside class="adm-sidebar" :class="{ 'is-open': open, 'is-collapsed': collapsed }">
            <x-admin.sidebar :site="$site" />
        </aside>

        {{-- Konten --}}
        <div class="adm-main">
            <header class="adm-topbar">
                <button type="button" class="adm-iconbtn" aria-label="Open or close menu"
                        @click="window.matchMedia('(min-width: 1024px)').matches ? collapsed = !collapsed : open = !open">
                    <x-admin.icon name="menu" class="h-6 w-6" />
                </button>

                <h1 class="adm-title">{{ $title }}</h1>

                <div class="adm-topbar-right">

                    <div class="adm-user">
                        <span class="adm-avatar">{{ mb_strtoupper(mb_substr($userName, 0, 1)) }}</span>
                        <span class="adm-user-text">
                            <span class="adm-user-name">{{ $userName }}</span>
                        </span>
                    </div>
                </div>
            </header>

            <main class="adm-content">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>