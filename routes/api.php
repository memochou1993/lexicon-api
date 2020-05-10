<?php

use Illuminate\Http\Request;
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
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('user', 'AuthController@user');
            Route::post('logout', 'AuthController@logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('projects', 'ProjectController');
        Route::apiResource('projects.users', 'ProjectUserController')
            ->only('store', 'destroy');
        Route::apiResource('projects.languages', 'ProjectLanguageController')
            ->only('store', 'destroy');
        Route::apiResource('languages', 'LanguageController');
        Route::apiResource('languages.forms', 'LanguageFormController')
            ->only('store', 'destroy');
        Route::apiResource('keys', 'KeyController');
    });
});
