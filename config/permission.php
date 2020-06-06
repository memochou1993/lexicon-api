<?php

use App\Enums\PermissionType as Permission;
use App\Enums\RoleType as Role;

return [

    'roles' => [

        'admin' => [

            'name' => Role::ADMINISTRATOR,

            'permissions' => [
                Permission::USER_VIEW_ANY,
                Permission::USER_VIEW,
                Permission::USER_UPDATE,
                Permission::USER_DELETE,
                Permission::ROLE_VIEW_ANY,
                Permission::ROLE_VIEW,
                Permission::ROLE_CREATE,
                Permission::ROLE_UPDATE,
                Permission::ROLE_DELETE,
                Permission::PERMISSION_VIEW_ANY,
                Permission::PERMISSION_VIEW,
                Permission::TEAM_VIEW_ANY,
                Permission::TEAM_VIEW,
                Permission::TEAM_CREATE,
                Permission::TEAM_UPDATE,
                Permission::TEAM_DELETE,
                Permission::PROJECT_VIEW_ANY,
                Permission::PROJECT_VIEW,
                Permission::PROJECT_CREATE,
                Permission::PROJECT_UPDATE,
                Permission::PROJECT_DELETE,
                Permission::LANGUAGE_VIEW_ANY,
                Permission::LANGUAGE_VIEW,
                Permission::LANGUAGE_CREATE,
                Permission::LANGUAGE_UPDATE,
                Permission::LANGUAGE_DELETE,
                Permission::FORM_VIEW_ANY,
                Permission::FORM_VIEW,
                Permission::FORM_CREATE,
                Permission::FORM_UPDATE,
                Permission::FORM_DELETE,
                Permission::KEY_VIEW_ANY,
                Permission::KEY_VIEW,
                Permission::KEY_CREATE,
                Permission::KEY_UPDATE,
                Permission::KEY_DELETE,
                Permission::VALUE_VIEW_ANY,
                Permission::VALUE_VIEW,
                Permission::VALUE_CREATE,
                Permission::VALUE_UPDATE,
                Permission::VALUE_DELETE,
                Permission::HOOK_VIEW_ANY,
                Permission::HOOK_VIEW,
                Permission::HOOK_CREATE,
                Permission::HOOK_UPDATE,
                Permission::HOOK_DELETE,
            ],

        ],

        'developer' => [

            'name' => Role::DEVELOPER,

            'permissions' => [
                Permission::TEAM_VIEW,
                Permission::PROJECT_VIEW_ANY,
                Permission::PROJECT_VIEW,
                Permission::PROJECT_CREATE,
                Permission::PROJECT_UPDATE,
                Permission::PROJECT_DELETE,
                Permission::KEY_VIEW_ANY,
                Permission::KEY_VIEW,
                Permission::KEY_CREATE,
                Permission::KEY_UPDATE,
                Permission::KEY_DELETE,
                Permission::VALUE_VIEW_ANY,
                Permission::VALUE_VIEW,
                Permission::VALUE_CREATE,
                Permission::VALUE_UPDATE,
                Permission::VALUE_DELETE,
                Permission::HOOK_VIEW_ANY,
                Permission::HOOK_VIEW,
                Permission::HOOK_CREATE,
                Permission::HOOK_UPDATE,
                Permission::HOOK_DELETE,
            ],

        ],

        'maintainer' => [

            'name' => Role::MAINTAINER,

            'permissions' => [
                Permission::TEAM_VIEW,
                Permission::PROJECT_VIEW_ANY,
                Permission::PROJECT_VIEW,
                Permission::KEY_VIEW_ANY,
                Permission::KEY_VIEW,
                Permission::KEY_CREATE,
                Permission::KEY_UPDATE,
                Permission::KEY_DELETE,
                Permission::VALUE_VIEW_ANY,
                Permission::VALUE_VIEW,
                Permission::VALUE_CREATE,
                Permission::VALUE_UPDATE,
                Permission::VALUE_DELETE,
            ],

        ],

    ],

];
