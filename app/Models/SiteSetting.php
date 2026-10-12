<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'site_name', 'logo', 'favicon',
    'tagline', 'heart_statement', 'impact_statement', 'about_text',
    'email', 'instagram_url', 'linkedin_url',
])]
class SiteSetting extends Model
{
    use HasTranslations;

    public array $translatable = ['tagline', 'heart_statement', 'impact_statement', 'about_text'];

    private static ?self $instance = null;

    /** Tabel ini hanya punya satu baris. Kalau belum ada, dibuat otomatis. */
    public static function current(): self
    {
        return self::$instance ??= static::query()->first()
            ?? static::create(['site_name' => 'Dream Ratio']);
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : null;
    }
} 