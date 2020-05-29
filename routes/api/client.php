<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('projects', 'ProjectController')
    ->only('show');
Route::apiResource('projects.cache', 'CacheController')
    ->only('destroy');
