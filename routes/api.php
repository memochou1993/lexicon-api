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
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('user', 'AuthController@user');
            Route::post('logout', 'AuthController@logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', 'UserController');
        Route::apiResource('teams', 'TeamController');
        Route::apiResource('teams.languages', 'TeamLanguageController')
            ->only('index', 'store');
        Route::apiResource('teams.projects', 'TeamProjectController')
            ->only('index', 'store');
        Route::apiResource('teams.users', 'TeamUserController');
        Route::apiResource('projects', 'ProjectController')
            ->only('show', 'update', 'destroy');
        Route::apiResource('projects.users', 'ProjectUserController')
            ->only('store', 'destroy');
        Route::apiResource('projects.languages', 'ProjectLanguageController')
            ->only('store', 'destroy');
        Route::apiResource('languages', 'LanguageController')
            ->only('show', 'update', 'destroy');
        Route::apiResource('languages.forms', 'LanguageFormController')
            ->only('store', 'destroy');
        Route::apiResource('forms', 'FormController');
        Route::apiResource('keys', 'KeyController');
        Route::apiResource('values', 'ValueController');
    });
});
