<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'site_name', 'logo', 'favicon',
    'tagline', 'heart_statement', 'impact_statement', 'about_text',
    'story', 'vision', 'mission',
    'about_image', 'about_image_2', 'about_image_3', 'about_image_4',
    'bts_title', 'bts_text', 'bts_video', 'bts_poster', 'bts_enabled',
    'gallery_title', 'gallery_text', 'gallery_enabled',
    'cta_title', 'cta_text',
    'email', 'instagram_url', 'linkedin_url',
    'image_focus',
])]
class SiteSetting extends Model
{
    use HasTranslations;

    public array $translatable = [
        'tagline', 'heart_statement', 'impact_statement', 'about_text',
        'story', 'vision', 'mission',
        'bts_title', 'bts_text', 'gallery_title', 'gallery_text', 'cta_title', 'cta_text', 'about_label', 'about_title', 'story_title', 'cta_label',
    ];

    private static ?self $instance = null;

    /** Tabel ini hanya punya satu baris. Kalau belum ada, dibuat otomatis. */
    public static function current(): self
    {
        return self::$instance ??= static::query()->first()
            ?? static::create(['site_name' => 'Dream Ratio']);
    }

    protected function casts(): array
    {
        return [
            'image_focus' => 'array',
            'bts_enabled' => 'boolean',
            'gallery_enabled' => 'boolean',
        ];
    }

    /** Saved position of a photo: x and y in percent, z = zoom. Defaults to centered and not zoomed. */
    public function focus(string $field): array
    {
        $f = $this->image_focus[$field] ?? [];

        return [
            'x' => (float) ($f['x'] ?? 50),
            'y' => (float) ($f['y'] ?? 50),
            'z' => (float) ($f['z'] ?? 1),
        ];
    }

    /**
     * Inline style for the <img> of a photo, e.g. style="{{ $site->imageStyle('about_image') }}".
     * The image must fill its frame (width and height 100%, object-fit: cover) and the frame needs overflow: hidden.
     */
    public function imageStyle(string $field): string
    {
        ['x' => $x, 'y' => $y, 'z' => $z] = $this->focus($field);
        $n = fn (float $v): string => rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');

        return sprintf(
            'object-position: %s%% %s%%; transform: scale(%s); transform-origin: %s%% %s%%;',
            $n($x), $n($y), $n($z), $n($x), $n($y)
        );
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