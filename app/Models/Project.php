<?php

namespace App\Models;

use App\Models\Traits\HasCache;
use App\Models\Traits\HasLanguages;
use App\Models\Traits\HasSetting;
use App\Models\Traits\HasUsers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Team $team
 * @property Setting $setting
 * @property Collection $hooks
 */
class Project extends Model
{
    use HasCache;
    use HasUsers;
    use HasLanguages;
    use HasSetting;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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

    /**
     * Get the hooks for the project.
     *
     * @return HasMany
     */
    public function hooks()
    {
        return $this->hasMany(Hook::class);
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        $tag = sprintf('%s:%', $this->getTable(), $this->getKey());

        return Cache::tags($tag)->sear('team', fn() => $this->team);
    }
}
