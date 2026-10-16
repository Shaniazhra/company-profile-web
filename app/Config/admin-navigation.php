<?php

/*
 * Menu sidebar admin. Tambah menu baru = tambah satu baris.
 * 'match' = pola route yang membuat menu tampil aktif.
 * Item yang route-nya belum terdaftar otomatis disembunyikan.
 */
return [
    [
        'group' => null,
        'items' => [
            ['label' => 'Dashboard', 'route' => 'admin.index', 'match' => 'admin.index', 'icon' => 'dashboard'],
        ],
    ],
    [
        'group' => 'Konten',
        'items' => [
            ['label' => 'Services', 'route' => 'admin.services.index', 'match' => 'admin.services.*', 'icon' => 'services'],
            ['label' => 'Projects', 'route' => 'admin.projects.index', 'match' => 'admin.projects.*', 'icon' => 'projects'],
            ['label' => 'Clients', 'route' => 'admin.clients.index', 'match' => 'admin.clients.*', 'icon' => 'clients'],
            ['label' => 'Tim', 'route' => 'admin.team.index', 'match' => 'admin.team.*', 'icon' => 'team'],
        ],
    ],
    [
        'group' => 'Settings',
        'items' => [
            ['label' => 'Site Settings', 'route' => 'admin.site.edit', 'match' => 'admin.site.*', 'icon' => 'settings'],
        ],
    ],
];