<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

trait HasUsers
{
    /**
     * Get all of the users for the model.
     *
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users');
    }

    /**
     * @return MorphToMany
     */
    public function owners()
    {
        return $this->users()->where('is_owner', true);
    }

    /**
     * Determine if the model has the given user.
     *
     * @param  User  $user
     * @return bool
     */
    public function hasCachedUser(User $user)
    {
        return $this->getCachedUsers()->contains($user);
    }

    /**
     * @return Collection
     */
    public function getCachedUsers(): Collection
    {
        $cacheKey = sprintf('%s:%d:users', $this->getTable(), $this->getKey());

        return Cache::sear($cacheKey, fn() => $this->users);
    }

    /**
     * @return bool
     */
    public function forgetCachedUsers(): bool
    {
        $cacheKey = sprintf('%s:%d:users', $this->getTable(), $this->getKey());

        return Cache::forget($cacheKey);
    }
}
