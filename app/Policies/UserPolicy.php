<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function viewAny(User $user)
    {
        if (! $user->tokenCan(PermissionType::USER_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function view(User $user, User $model)
    {
        if (! $user->tokenCan(PermissionType::USER_VIEW)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function create(User $user)
    {
        if (! $user->tokenCan(PermissionType::USER_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function update(User $user, User $model)
    {
        if (! $user->tokenCan(PermissionType::USER_UPDATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function delete(User $user, User $model)
    {
        if (! $user->tokenCan(PermissionType::USER_DELETE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }
}
