<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInProjectException;
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
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function viewAny(User $user)
    {
        if (! $user->tokenCan(PermissionType::KEY_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function view(User $user, Key $key)
    {
        if (! $user->tokenCan(PermissionType::KEY_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $key->getCachedProject()->hasCachedUser($user)) {
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
        if (! $user->tokenCan(PermissionType::KEY_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function update(User $user, Key $key)
    {
        if (! $user->tokenCan(PermissionType::KEY_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $key->getCachedProject()->hasCachedUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Key  $key
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function delete(User $user, Key $key)
    {
        if (! $user->tokenCan(PermissionType::KEY_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $key->getCachedProject()->hasCachedUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }
}
