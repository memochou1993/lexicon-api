<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInProjectException;
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
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function viewAny(User $user)
    {
        if (! $user->tokenCan(PermissionType::VALUE_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function view(User $user, Value $value)
    {
        if (! $user->tokenCan(PermissionType::VALUE_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $value->getCachedProject()->hasCachedUser($user)) {
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
        if (! $user->tokenCan(PermissionType::VALUE_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function update(User $user, Value $value)
    {
        if (! $user->tokenCan(PermissionType::VALUE_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $value->getCachedProject()->hasCachedUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Value  $value
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function delete(User $user, Value $value)
    {
        if (! $user->tokenCan(PermissionType::VALUE_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $value->getCachedProject()->hasCachedUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }
}
