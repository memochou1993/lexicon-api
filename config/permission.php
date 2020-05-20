<?php

return [

    'roles' => [

        'admin' => [
            'name' => 'administrator',
            'permissions' => [
                // TODO
                'update-teams',
                'update-projects',
                'update-keys',
            ],
        ],

        'owner' => [
            'name' => 'owner',
            'permissions' => [
                // TODO
                'update-projects',
                'update-keys',
            ],
        ],

        'maintainer' => [
            'name' => 'maintainer',
            'permissions' => [
                // TODO
                'update-keys',
            ],
        ],

    ],

];
