<?php

namespace App\Policies;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FormPolicy
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
        if (! $user->tokenCan(PermissionType::FORM_VIEW_ANY)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     */
    public function view(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_VIEW)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($form->teams->first())) {
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
        if (! $user->tokenCan(PermissionType::FORM_CREATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     */
    public function update(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_UPDATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($form->teams->first())) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return mixed
     */
    public function delete(User $user, Form $form)
    {
        if (! $user->tokenCan(PermissionType::FORM_DELETE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($form->teams->first())) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }
}
