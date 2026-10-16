<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasListScopes;

    protected $fillable = ['question', 'answer', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

}