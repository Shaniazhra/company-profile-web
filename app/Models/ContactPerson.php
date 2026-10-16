<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use HasListScopes;

    protected $fillable = ['name', 'role', 'phone', 'email', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

}