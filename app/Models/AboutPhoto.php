<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'alt', 'sort_order', 'is_active', 'zoom', 'focus_x', 'focus_y'])]
class AboutPhoto extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'zoom' => 'float',
            'focus_x' => 'integer',
            'focus_y' => 'integer',
        ];
    }

    /** Only photos that are switched on. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** First photo first. The id keeps the order stable when two photos share a number. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    /**
     * Inline style for the <img>: zoom (1 = the photo just fills its frame, 3 = three times closer)
     * and which part of the photo stays in view. Used on the website and in the admin preview.
     */
    public function imageStyle(): string
    {
        $zoom = max(1, min(3, (float) ($this->zoom ?? 1)));
        $x = max(0, min(100, (int) ($this->focus_x ?? 50)));
        $y = max(0, min(100, (int) ($this->focus_y ?? 50)));

        return sprintf('--z: %s; object-position: %d%% %d%%; transform-origin: %d%% %d%%;', rtrim(rtrim(number_format($zoom, 2, '.', ''), '0'), '.'), $x, $y, $x, $y);
    }
}