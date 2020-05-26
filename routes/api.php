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
        Route::apiResource('roles', 'RoleController');
        Route::apiResource('permissions', 'PermissionController')
            ->only('index', 'show');

        Route::apiResource('users', 'UserController')
            ->except('create');
        Route::apiResource('teams', 'TeamController')
            ->except('create');

        Route::namespace('User')->prefix('user')->group(function () {
            Route::apiResource('teams', 'TeamController')
                ->only('index', 'store');
            Route::apiResource('projects', 'ProjectController')
                ->only('index');
        });

        Route::apiResource('teams.projects', 'ProjectController')
            ->shallow();
        Route::apiResource('teams.languages', 'LanguageController')
            ->shallow()->except('index');
        Route::apiResource('teams.forms', 'FormController')
            ->shallow()->except('index');
        Route::apiResource('projects.keys', 'KeyController')
            ->shallow();
        Route::apiResource('keys.values', 'ValueController')
            ->shallow()->except('index');

        Route::apiResource('users.roles', 'UserRoleController')
            ->only('store', 'destroy');
        Route::apiResource('teams.users', 'TeamUserController')
            ->only('store', 'destroy');
        Route::apiResource('projects.users', 'ProjectUserController')
            ->only('store', 'destroy');
        Route::apiResource('projects.languages', 'ProjectLanguageController')
            ->only('store', 'destroy');
    });
});
