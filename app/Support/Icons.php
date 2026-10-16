<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class Icons
{
    /** key => [label, isi SVG gaya garis 24x24] */
    public static function all(): array
    {
        return [
            'video'   => ['Video', '<path d="M3 7h13a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H3z"/><path d="M18 11l4-2.5v7L18 13"/>'],
            'film'    => ['Film', '<rect x="2" y="2" width="20" height="20" rx="2.2"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/>'],
            'camera'  => ['Kamera', '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>'],
            'layers'  => ['Lapisan', '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>'],
            'cube'    => ['3D', '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><path d="M12 22.08V12"/>'],
            'sun'     => ['Cahaya', '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>'],
            'user'    => ['Characters', '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4-6 8-6s7 2 8 6"/>'],
            'users'   => ['Tim', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>'],
            'clock'   => ['Waktu', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
            'coin'    => ['Biaya', '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5c0-1 1-1.8 2.5-1.8s2.5.8 2.5 1.8-1 1.6-2.5 2-2.5 1-2.5 2 1 1.8 2.5 1.8 2.5-.8 2.5-1.8M12 6v1.7M12 16.3V18"/>'],
            'star'    => ['Bintang', '<polygon points="12 2 15.1 8.6 22 9.3 16.8 14 18.3 21 12 17.5 5.7 21 7.2 14 2 9.3 8.9 8.6"/>'],
            'mic'     => ['Suara', '<path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v4M8 23h8"/>'],
            'brush'   => ['Desain', '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
            'monitor' => ['Layar', '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>'],
            'bolt'    => ['Cepat', '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'],
            'target'  => ['Target', '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>'],
            'bulb'    => ['Ide', '<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3z"/>'],
            'sparkle' => ['Efek', '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15z"/>'],
            'chart'   => ['Analisis', '<path d="M12 20V10M18 20V4M6 20v-4"/>'],
            'search'  => ['Riset', '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>'],
        ];
    }

    public static function svg(?string $key, string $class = ''): HtmlString
    {
        $icons = self::all();
        $icon = $icons[$key] ?? $icons['star'];
        $attr = $class !== '' ? ' class="'.e($class).'"' : '';

        return new HtmlString(
            '<svg'.$attr.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" '
            .'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$icon[1].'</svg>'
        );
    }
}