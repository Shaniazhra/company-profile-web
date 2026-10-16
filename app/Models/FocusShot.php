<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class FocusShot extends Model
{
    use HasListScopes;

    protected $fillable = ['title', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}