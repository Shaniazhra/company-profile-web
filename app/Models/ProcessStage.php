<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class ProcessStage extends Model
{
    use HasListScopes;

    protected $fillable = ['title', 'summary', 'details', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

}