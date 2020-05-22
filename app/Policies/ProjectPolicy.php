<?php

namespace App\Policies;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_VIEW_ANY)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_VIEW)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasProject($project)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_PROJECT);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_CREATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_UPDATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasProject($project)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_PROJECT);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        if (! $user->tokenCan(PermissionType::PROJECT_DELETE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasProject($project)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_PROJECT);
        }

        return true;
    }
}
