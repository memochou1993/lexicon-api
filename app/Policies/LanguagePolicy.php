<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LanguagePolicy
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
        return $user->tokenCan('view-language');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return bool
     */
    public function view(User $user, Language $language)
    {
        return $user->tokenCan('view-language')
            && $user->hasTeam($language->teams->first());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->tokenCan('create-language');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return bool
     */
    public function update(User $user, Language $language)
    {
        return $user->tokenCan('update-language')
            && $user->hasTeam($language->teams->first());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return bool
     */
    public function delete(User $user, Language $language)
    {
        return $user->tokenCan('delete-language')
            && $user->hasTeam($language->teams->first());
    }
}
