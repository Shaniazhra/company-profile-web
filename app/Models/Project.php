<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasTranslations;

    public array $translatable = ['tag', 'synopsis'];

    /** Allowlist of fields that can be mass-assigned. */
    protected $fillable = [
        'tag', 'synopsis', 'slug', 'image', 'logo', 'youtube_url',
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

    /** Readable name for admin lists and logo alt text, built from the slug ("ibra-season-2" becomes "Ibra Season 2"). */
    protected function name(): Attribute
    {
        return Attribute::get(fn () => Str::headline($this->slug ?: 'Project'));
    }

    /** Title logo URL, or null if none has been uploaded. */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->logo ? asset('storage/'.$this->logo) : null);
    }

    /** Project image URL, or null. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? asset('storage/'.$this->image) : null);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Display order: lowest order number first, then newest. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}