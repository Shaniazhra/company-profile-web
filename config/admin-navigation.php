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
        'group' => 'Content',
        'items' => [
            ['label' => 'Services', 'route' => 'admin.services.index', 'match' => 'admin.services.*', 'icon' => 'services'],
            ['label' => 'Projects', 'route' => 'admin.projects.index', 'match' => 'admin.projects.*', 'icon' => 'projects'],
            ['label' => 'Clients', 'route' => 'admin.clients.index', 'match' => 'admin.clients.*', 'icon' => 'clients'],
            ['label' => 'Team', 'route' => 'admin.team.index', 'match' => 'admin.team.*', 'icon' => 'team'],
            ['label' => 'Process', 'route' => 'admin.process.index', 'match' => 'admin.process.*', 'icon' => 'projects'],
            ['label' => 'FAQ', 'route' => 'admin.faqs.index', 'match' => 'admin.faqs.*', 'icon' => 'services'],
        ],
    ],
    [
        'group' => 'Inbox',
        'items' => [
            ['label' => 'Contacts', 'route' => 'admin.contacts.index', 'match' => 'admin.contacts.*', 'icon' => 'user'],
            ['label' => 'Inquiries', 'route' => 'admin.inquiries.index', 'match' => 'admin.inquiries.*', 'icon' => 'clients'],
        ],
    ],
    [
        'group' => 'Settings',
        'items' => [
            ['label' => 'Site Settings', 'route' => 'admin.site.edit', 'match' => 'admin.site.*', 'icon' => 'settings'],
        ],
    ],
];
