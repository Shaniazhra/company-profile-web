<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable(['type', 'title', 'sub_points', 'sort_order', 'is_published'])]
class PipelineStep extends Model
{
    use HasTranslations;

    /** Kolom yang punya versi ID dan EN. */
    public array $translatable = ['title', 'sub_points'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}