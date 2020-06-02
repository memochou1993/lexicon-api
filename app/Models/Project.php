<?php

namespace App\Models;

use App\Models\Traits\HasCache;
use App\Models\Traits\HasLanguages;
use App\Models\Traits\HasUsers;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Sanctum\HasApiTokens;

class Project extends Model
{
    use Authenticatable;
    use HasApiTokens;
    use HasCache;
    use HasUsers;
    use HasLanguages;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'api_keys',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'team_id',
    ];

    /**
     * Get the team that owns the project.
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the keys for the project.
     *
     * @return HasMany
     */
    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    /**
     * Get all of the values for the project.
     *
     * @return HasManyThrough
     */
    public function values()
    {
        return $this->hasManyThrough(Value::class, Key::class);
    }
}
