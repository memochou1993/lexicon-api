<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Key;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeyPolicy
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
        return $user->tokenCan(PermissionType::KEY_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return bool
     */
    public function view(User $user, Key $key)
    {
        return $user->tokenCan(PermissionType::KEY_VIEW)
            && $user->hasProject($key->project);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->tokenCan(PermissionType::KEY_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return bool
     */
    public function update(User $user, Key $key)
    {
        return $user->tokenCan(PermissionType::KEY_UPDATE)
            && $user->hasProject($key->project);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return bool
     */
    public function delete(User $user, Key $key)
    {
        return $user->tokenCan(PermissionType::KEY_DELETE)
            && $user->hasProject($key->project);
    }
}
