<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;

class CrewMember extends Model
{
    use HasListScopes;

    protected $fillable = ['name', 'role', 'photo', 'credits', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected function photoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(fn () => $this->photo ? asset('storage/'.$this->photo) : null);
    }

    /** Credits are typed one per line in the admin panel. */
    public function creditList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->credits))));
    }
}