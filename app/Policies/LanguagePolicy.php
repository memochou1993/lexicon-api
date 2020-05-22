<?php

namespace App\Policies;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LanguagePolicy
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
        if (! $user->tokenCan(PermissionType::LANGUAGE_VIEW_ANY)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return mixed
     */
    public function view(User $user, Language $language)
    {
        if (! $user->tokenCan(PermissionType::LANGUAGE_VIEW)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($language->teams->first())) {
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
        if (! $user->tokenCan(PermissionType::LANGUAGE_CREATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return mixed
     */
    public function update(User $user, Language $language)
    {
        if (! $user->tokenCan(PermissionType::LANGUAGE_UPDATE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($language->teams->first())) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Language  $language
     * @return mixed
     */
    public function delete(User $user, Language $language)
    {
        if (! $user->tokenCan(PermissionType::LANGUAGE_DELETE)) {
            return Response::deny(null, ErrorType::PERMISSION_DENIED);
        }

        if (! $user->hasTeam($language->teams->first())) {
            return Response::deny(null, ErrorType::USER_NOT_IN_TEAM);
        }

        return true;
    }
}
