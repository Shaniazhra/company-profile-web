<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    use HasListScopes;

    protected $fillable = ['group', 'title', 'text', 'icon', 'sort_order', 'is_active'];

    // Ikon Feather (inline SVG, memakai currentColor). Admin memilih salah satu kunci.
    public const ICONS = [
        'layers' => ['Layers', '<path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>'],
        'sun'    => ['Sun', '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>'],
        'bolt'   => ['Lightning', '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>'],
        'users'  => ['People', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        'star'   => ['Star', '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'],
        'clock'  => ['Clock', '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>'],
        'film'   => ['Film', '<rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/>'],
        'check'  => ['Check', '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/>'],
        'heart'  => ['Heart', '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>'],
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getIconPathsAttribute(): string
    {
        return self::ICONS[$this->icon][1] ?? self::ICONS['star'][1];
    }

    public function scopeReasons($query)
    {
        return $query->active()->ordered()->where('group', 'reason');
    }

    public function scopeResults($query)
    {
        return $query->active()->ordered()->where('group', 'result');
    }
}