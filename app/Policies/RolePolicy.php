<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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
        if (! $user->tokenCan(PermissionType::ROLE_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function view(User $user, Role $role)
    {
        if (! $user->tokenCan(PermissionType::ROLE_VIEW)) {
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
        if (! $user->tokenCan(PermissionType::ROLE_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function update(User $user, Role $role)
    {
        if (! $user->tokenCan(PermissionType::ROLE_UPDATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function delete(User $user, Role $role)
    {
        if (! $user->tokenCan(PermissionType::ROLE_DELETE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }
}
