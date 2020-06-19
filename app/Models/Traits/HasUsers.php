<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property Collection $users
 * @property Collection $owners
 */
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
     * Get all of the owners for the model.
     *
     * @return MorphToMany
     */
    public function owners(): MorphToMany
    {
        return $this->users()->where('is_owner', true);
    }

    /**
     * Determine if the model has the given user.
     *
     * @param  User  $user
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        $tag = sprintf('%s:%', $this->getTable(), $this->getKey());

        return Cache::tags($tag)->sear('owner', fn() => $this->owners->first());
    }
}
