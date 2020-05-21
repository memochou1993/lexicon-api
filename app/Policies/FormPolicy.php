<?php

namespace App\Policies;

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
        return $user->hasPermission('view-form');
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
        return $user->hasPermission('view-form')
            && $form->teams->first()->hasUser($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-form');
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
        return $user->hasPermission('update-form')
            && $form->teams->first()->hasUser($user);
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
        return $user->hasPermission('delete-form')
            && $form->teams->first()->hasUser($user);
    }
}
