<?php

namespace App\Policies;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInTeamException;
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
     * @throws PermissionDeniedException
     */
    public function viewAny(User $user)
    {
        if (! $user->tokenCan(PermissionType::TEAM_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function view(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($team)) {
            throw new UserNotInTeamException();
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
        if (! $user->tokenCan(PermissionType::TEAM_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function update(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($team)) {
            throw new UserNotInTeamException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function delete(User $user, Team $team)
    {
        if (! $user->tokenCan(PermissionType::TEAM_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($team)) {
            throw new UserNotInTeamException();
        }

        return true;
    }
}
