<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::get('/', 'UserController@show');
    Route::patch('/', 'UserController@update');
    Route::apiResource('teams', 'TeamController')
        ->only('index', 'store');
    Route::apiResource('projects', 'ProjectController')
        ->only('index');
});
