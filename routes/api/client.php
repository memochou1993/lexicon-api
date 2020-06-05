<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'token:project',
    'auth:sanctum',
])->group(function () {
    Route::get('project', 'ProjectController@show');
    Route::delete('project/cache', 'ProjectCacheController@destroy');
});
