<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
     * Determine if the model has the given user.
     *
     * @param  User  $user
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }
}
