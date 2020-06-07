<?php

namespace App\Models;

use App\Models\Traits\HasCache;
use App\Models\Traits\HasLanguages;
use App\Models\Traits\HasTokens;
use App\Models\Traits\HasUsers;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Team $team
 * @property Collection $tokens
 * @property Collection $users
 * @property Collection $owners
 * @property Collection $languages
 * @property Collection $keys
 * @property Collection $values
 * @property Collection $hooks
 */
class Project extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasApiTokens, HasTokens {
        HasTokens::tokens insteadof HasApiTokens;
    }
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
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @return NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        /** @var Token $token */
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    /**
     * @return Team
     */
    public function getCachedTeam(): Team
    {
        $cacheKey = sprintf('%s:%d:team', $this->getTable(), $this->getKey());

        return Cache::sear($cacheKey, fn() => $this->team);
    }
}
