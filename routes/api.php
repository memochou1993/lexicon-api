<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('user', 'AuthController@getUser');
            Route::patch('user', 'AuthController@updateUser');
            Route::post('logout', 'AuthController@logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', 'UserController')
            ->only('index', 'show', 'update', 'destroy');
        Route::apiResource('users.roles', 'UserRoleController')
            ->only('store', 'destroy');

        Route::apiResource('roles', 'RoleController');

        Route::prefix('user')->group(function () {
            Route::apiResource('teams', 'UserTeamController')
                ->only('index', 'store');
        });

        Route::apiResource('teams', 'TeamController')
            ->only('show', 'update', 'destroy');
        Route::apiResource('teams.users', 'TeamUserController')
            ->only('store', 'destroy');
        Route::apiResource('teams.projects', 'TeamProjectController')
            ->only('index', 'store');
        Route::apiResource('teams.languages', 'TeamLanguageController')
            ->only('store');
        Route::apiResource('teams.forms', 'TeamFormController')
            ->only('store');

        Route::apiResource('projects', 'ProjectController')
            ->only('show', 'update', 'destroy');
        Route::apiResource('projects.users', 'ProjectUserController')
            ->only('store', 'destroy');
        Route::apiResource('projects.languages', 'ProjectLanguageController')
            ->only('store', 'destroy');
        Route::apiResource('projects.keys', 'ProjectKeyController')
            ->only('index', 'store');

        Route::apiResource('languages', 'LanguageController')
            ->only('show', 'update', 'destroy');

        Route::apiResource('forms', 'FormController')
            ->only('show', 'update', 'destroy');

        Route::apiResource('keys', 'KeyController')
            ->only('show', 'update', 'destroy');
        Route::apiResource('keys.values', 'KeyValueController')
            ->only('store');

        Route::apiResource('values', 'ValueController')
            ->only('show', 'update', 'destroy');
    });
});
