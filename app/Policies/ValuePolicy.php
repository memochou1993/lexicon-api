<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Value;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->tokenCan('view-value');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return bool
     */
    public function view(User $user, Value $value)
    {
        return $user->tokenCan('view-value')
            && $user->hasProject($value->key->project);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->tokenCan('create-value');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return bool
     */
    public function update(User $user, Value $value)
    {
        return $user->tokenCan('update-value')
            && $user->hasProject($value->key->project);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return bool
     */
    public function delete(User $user, Value $value)
    {
        return $user->tokenCan('delete-value')
            && $user->hasProject($value->key->project);
    }
}
