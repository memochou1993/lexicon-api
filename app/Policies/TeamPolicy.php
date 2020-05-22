<?php

namespace App\Policies;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\User;
use App\Models\Team;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TeamPolicy
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
        if (! $user->tokenCan(PermissionType::TEAM_VIEW_ANY)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     */
    public function view(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_VIEW)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($team)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
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
        return $user->tokenCan(PermissionType::TEAM_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_UPDATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($team)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     */
    public function delete(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_DELETE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($team)) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }
}
