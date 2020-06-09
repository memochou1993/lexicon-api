<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInProjectException;
use App\Models\Hook;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HookPolicy
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
        if (! $user->tokenCan(PermissionType::HOOK_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Hook  $hook
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function view(User $user, Hook $hook)
    {
        if (! $user->tokenCan(PermissionType::HOOK_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $hook->getProject()->hasUser($user)) {
            throw new UserNotInProjectException();
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
        if (! $user->tokenCan(PermissionType::HOOK_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Hook  $hook
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function update(User $user, Hook $hook)
    {
        if (! $user->tokenCan(PermissionType::HOOK_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $hook->getProject()->hasUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Hook  $hook
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function delete(User $user, Hook $hook)
    {
        if (! $user->tokenCan(PermissionType::HOOK_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $hook->getProject()->hasUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }
}
