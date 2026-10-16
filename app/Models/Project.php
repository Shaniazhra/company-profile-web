<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'tag', 'synopsis'];

    /** Allowlist field yang boleh diisi massal. */
    protected $fillable = [
        'title', 'tag', 'synopsis', 'slug', 'image', 'logo', 'youtube_url',
        'release_year', 'is_published', 'sort_order', 'characters',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'release_year' => 'integer',
            'sort_order'   => 'integer',
            'characters'   => 'array',
        ];
    }

    /** URL logo judul, atau null kalau belum diunggah (pakai judul teks). */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->logo ? asset('storage/'.$this->logo) : null);
    }

    /** URL gambar proyek, atau null. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? asset('storage/'.$this->image) : null);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Urutan tampil: angka urutan kecil dulu, lalu yang terbaru. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}