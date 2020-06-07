<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Exceptions\PermissionDeniedException;
use App\Exceptions\UserNotInTeamException;
use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
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
        if (! $user->tokenCan(PermissionType::FORM_VIEW_ANY)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function view(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_VIEW)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($form->getCachedTeam())) {
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
        if (! $user->tokenCan(PermissionType::FORM_CREATE)) {
            throw new PermissionDeniedException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function update(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_UPDATE)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($form->getCachedTeam())) {
            throw new UserNotInTeamException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     * @throws PermissionDeniedException
     * @throws UserNotInTeamException
     */
    public function delete(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_DELETE)) {
            throw new PermissionDeniedException();
        }

        if (! $user->hasTeam($form->getCachedTeam())) {
            throw new UserNotInTeamException();
        }

        return true;
    }
}
