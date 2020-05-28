<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'UserController@show');
Route::patch('/', 'UserController@update');
Route::apiResource('teams', 'TeamController')
    ->only('index', 'store');
Route::apiResource('projects', 'ProjectController')
    ->only('index');
