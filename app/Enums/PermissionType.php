<?php

namespace App\Enums;

use App\Enums\Contracts\EnumContract;

final class PermissionType extends AbstractEnum implements EnumContract
{
    const USER_VIEW_ANY = 'view-any-users';
    const USER_VIEW = 'view-users';
    const USER_CREATE = 'create-users';
    const USER_UPDATE = 'update-users';
    const USER_DELETE = 'delete-users';
    const USER_RESTORE = 'restore-users';
    const USER_FORCE_DELETE = 'force-delete-users';
    const ROLE_VIEW_ANY = 'view-any-roles';
    const ROLE_VIEW = 'view-roles';
    const ROLE_CREATE = 'create-roles';
    const ROLE_UPDATE = 'update-roles';
    const ROLE_DELETE = 'delete-roles';
    const ROLE_RESTORE = 'restore-roles';
    const ROLE_FORCE_DELETE = 'force-delete-permissions';
    const PERMISSION_VIEW_ANY = 'view-any-permissions';
    const PERMISSION_VIEW = 'view-permissions';
    const PERMISSION_CREATE = 'create-permissions';
    const PERMISSION_UPDATE = 'update-permissions';
    const PERMISSION_DELETE = 'delete-permissions';
    const PERMISSION_RESTORE = 'restore-permissions';
    const PERMISSION_FORCE_DELETE = 'force-delete-permissions';
    const TEAM_VIEW_ANY = 'view-any-teams';
    const TEAM_VIEW = 'view-teams';
    const TEAM_CREATE = 'create-teams';
    const TEAM_UPDATE = 'update-teams';
    const TEAM_DELETE = 'delete-teams';
    const TEAM_RESTORE = 'restore-teams';
    const TEAM_FORCE_DELETE = 'force-delete-teams';
    const PROJECT_VIEW_ANY = 'view-any-projects';
    const PROJECT_VIEW = 'view-projects';
    const PROJECT_CREATE = 'create-projects';
    const PROJECT_UPDATE = 'update-projects';
    const PROJECT_DELETE = 'delete-projects';
    const PROJECT_RESTORE = 'restore-projects';
    const PROJECT_FORCE_DELETE = 'force-delete-projects';
    const LANGUAGE_VIEW_ANY = 'view-any-languages';
    const LANGUAGE_VIEW = 'view-languages';
    const LANGUAGE_CREATE = 'create-languages';
    const LANGUAGE_UPDATE = 'update-languages';
    const LANGUAGE_DELETE = 'delete-languages';
    const LANGUAGE_RESTORE = 'restore-languages';
    const LANGUAGE_FORCE_DELETE = 'force-delete-languages';
    const FORM_VIEW_ANY = 'view-any-forms';
    const FORM_VIEW = 'view-forms';
    const FORM_CREATE = 'create-forms';
    const FORM_UPDATE = 'update-forms';
    const FORM_DELETE = 'delete-forms';
    const FORM_RESTORE = 'restore-forms';
    const FORM_FORCE_DELETE = 'force-delete-forms';
    const KEY_VIEW_ANY = 'view-any-keys';
    const KEY_VIEW = 'view-keys';
    const KEY_CREATE = 'create-keys';
    const KEY_DELETE = 'delete-keys';
    const KEY_UPDATE = 'update-keys';
    const KEY_RESTORE = 'restore-keys';
    const KEY_FORCE_DELETE = 'force-delete-keys';
    const VALUE_VIEW_ANY = 'view-any-values';
    const VALUE_VIEW = 'view-values';
    const VALUE_CREATE = 'create-values';
    const VALUE_UPDATE = 'update-values';
    const VALUE_DELETE = 'delete-values';
    const VALUE_RESTORE = 'restore-values';
    const VALUE_FORCE_DELETE = 'force-delete-values';
    const HOOK_VIEW_ANY = 'view-any-hooks';
    const HOOK_VIEW = 'view-hooks';
    const HOOK_CREATE = 'create-hooks';
    const HOOK_DELETE = 'delete-hooks';
    const HOOK_UPDATE = 'update-hooks';
    const HOOK_RESTORE = 'restore-hooks';
    const HOOK_FORCE_DELETE = 'force-delete-hooks';
}
