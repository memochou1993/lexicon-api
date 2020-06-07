<?php

namespace App\Models;

use App\Models\Traits\HasForms;
use App\Models\Traits\HasLanguages;
use App\Models\Traits\HasUsers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $users
 * @property Collection $owners
 * @property Collection $projects
 * @property Collection $languages
 * @property Collection $forms
 */
class Team extends Model
{
    use HasUsers;
    use HasLanguages;
    use HasForms;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the projects for the team.
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return Collection
     */
    public function getCachedUsers(): Collection
    {
        $cacheKey = sprintf('%s:%d:users', $this->getTable(), $this->id);

        return Cache::sear($cacheKey, fn() => $this->users);
    }

    /**
     * @return bool
     */
    public function forgetCachedUsers(): bool
    {
        $cacheKey = sprintf('%s:%d:users', $this->getTable(), $this->id);

        return Cache::forget($cacheKey);
    }
}
