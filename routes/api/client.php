<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('projects.keys', 'KeyController')
    ->only('index');
Route::apiResource('projects.cache', 'CacheController')
    ->only('destroy');
