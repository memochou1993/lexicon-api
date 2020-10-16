<?php

use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\HookController;
use App\Http\Controllers\Api\KeyController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectLanguageController;
use App\Http\Controllers\Api\ProjectUserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\ValueController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::apiResource('roles', RoleController::class);

    Route::apiResource('permissions', PermissionController::class)
        ->only('index', 'show');

    Route::apiResource('users', UserController::class)
        ->except('create');

    Route::apiResource('teams', TeamController::class)
        ->except('create');
    Route::apiResource('teams.projects', ProjectController::class)
        ->shallow();
    Route::apiResource('teams.languages', LanguageController::class)
        ->shallow()->except('index');
    Route::apiResource('teams.forms', FormController::class)
        ->shallow()->except('index');

    Route::apiResource('projects.keys', KeyController::class)
        ->shallow();
    Route::apiResource('projects.hooks', HookController::class)
        ->shallow()->except('index');

    Route::apiResource('keys.values', ValueController::class)
        ->shallow()->except('index');

    Route::apiResources([
        'users.roles' => UserRoleController::class,
        'teams.users' => TeamUserController::class,
        'projects.users' => ProjectUserController::class,
        'projects.languages' => ProjectLanguageController::class,
    ], [
        'only' => [
            'store',
            'destroy',
        ],
    ]);
});
