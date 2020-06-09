<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInProjectException;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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
        if (! $user->tokenCan(PermissionType::PROJECT_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function view(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $project->hasUser($user)) {
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
        if (! $user->tokenCan(PermissionType::PROJECT_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function update(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $project->hasUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInProjectException
     */
    public function delete(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $project->hasUser($user)) {
            throw new UserNotInProjectException();
        }

        return true;
    }
}
