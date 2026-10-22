<?php

namespace App\Models;

use App\Models\Concerns\HasListScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewMember extends Model
{
    use HasListScopes;

    protected $fillable = ['name', 'role', 'photo', 'credits', 'sort_order', 'is_active', 'team_division_id', 'is_featured', 'photo_x', 'photo_y', 'photo_zoom'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_featured' => 'boolean'];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(TeamDivision::class, 'team_division_id');
    }

    /**
     * The few people shown on the Home and About pages: the ones marked "Show on Home and About" (at most $limit).
     * When nobody is marked yet, the first $limit active people are used.
     */
    public static function homeList(int $limit = 3)
    {
        $featured = static::active()->ordered()->where('is_featured', true)->take($limit)->get();

        return $featured->isNotEmpty() ? $featured : static::active()->ordered()->take($limit)->get();
    }

    protected function photoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(fn () => $this->photo ? asset('storage/'.$this->photo) : null);
    }

    /** CSS variables with the position and zoom of the photo inside its card (set in the admin). */
    public function photoStyle(): string
    {
        $n = fn ($value, $default) => rtrim(rtrim(number_format((float) ($value ?? $default), 2, '.', ''), '0'), '.');

        return '--fx:'.$n($this->photo_x, 50).'%;--fy:'.$n($this->photo_y, 100).'%;--z:'.$n($this->photo_zoom, 1);
    }

    /** Credits are typed one per line in the admin panel. */
    public function creditList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->credits))));
    }
}