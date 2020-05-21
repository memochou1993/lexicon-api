<?php

return [

    'roles' => [

        'admin' => [

            'name' => 'Administrator',

            'permissions' => [
                'view-user',
                'update-user',
                'delete-user',
                'view-role',
                'create-role',
                'update-role',
                'delete-role',
                'view-team',
                'create-team',
                'update-team',
                'delete-team',
                'view-project',
                'create-project',
                'update-project',
                'delete-project',
                'view-language',
                'create-language',
                'update-language',
                'delete-language',
                'view-form',
                'create-form',
                'update-form',
                'delete-form',
                'view-key',
                'create-key',
                'delete-key',
                'update-key',
                'view-value',
                'create-value',
                'update-value',
                'delete-value',
            ],

        ],

        'developer' => [

            'name' => 'Developer',

            'permissions' => [
                'view-team',
                'view-project',
                'create-project',
                'update-project',
                'delete-project',
                'view-key',
                'create-key',
                'delete-key',
                'update-key',
                'view-value',
                'create-value',
                'update-value',
                'delete-value',
            ],

        ],

        'maintainer' => [

            'name' => 'Maintainer',

            'permissions' => [
                'view-team',
                'view-project',
                'view-key',
                'create-key',
                'delete-key',
                'update-key',
                'view-value',
                'create-value',
                'update-value',
                'delete-value',
            ],

        ],

    ],

];
