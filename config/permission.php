<?php

return [

    'roles' => [

        'admin' => [

            'name' => 'Administrator',

            'permissions' => [
                'view-users',
                'update-users',
                'delete-users',
                'view-roles',
                'create-roles',
                'update-roles',
                'delete-roles',
                'view-teams',
                'create-teams',
                'update-teams',
                'delete-teams',
                'view-projects',
                'create-projects',
                'update-projects',
                'delete-projects',
                'view-keys',
                'create-keys',
                'delete-keys',
                'update-keys',
                'view-values',
                'create-values',
                'update-values',
                'delete-values',
            ],

        ],

        'developer' => [

            'name' => 'Developer',

            'permissions' => [
                'view-teams',
                'view-projects',
                'create-projects',
                'update-projects',
                'delete-projects',
                'view-keys',
                'create-keys',
                'delete-keys',
                'update-keys',
                'view-values',
                'create-values',
                'update-values',
                'delete-values',
            ],

        ],

        'maintainer' => [

            'name' => 'Maintainer',

            'permissions' => [
                'view-teams',
                'view-projects',
                'view-keys',
                'create-keys',
                'delete-keys',
                'update-keys',
                'view-values',
                'create-values',
                'update-values',
                'delete-values',
            ],

        ],

    ],

];
