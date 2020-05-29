<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('projects', 'ProjectController')
    ->only('show');
Route::delete('projects/{project}/cache', 'ProjectCacheController@destroy');
