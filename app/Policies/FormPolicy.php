<?php

namespace App\Policies;

use App\Enums\PermissionType;
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
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->tokenCan(PermissionType::FORM_VIEW_ANY);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return bool
     */
    public function view(User $user, Form $form)
    {
        return $user->tokenCan(PermissionType::FORM_VIEW)
            && $user->hasTeam($form->teams->first());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->tokenCan(PermissionType::FORM_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return bool
     */
    public function update(User $user, Form $form)
    {
        return $user->tokenCan(PermissionType::FORM_UPDATE)
            && $user->hasTeam($form->teams->first());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Form  $form
     * @return bool
     */
    public function delete(User $user, Form $form)
    {
        return $user->tokenCan(PermissionType::FORM_DELETE)
            && $user->hasTeam($form->teams->first());
    }
}
