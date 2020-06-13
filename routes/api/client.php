<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'client',
])->group(function () {
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/', 'ProjectController@show');
        Route::delete('cache', 'ProjectCacheController@destroy');
        Route::post('dispatch', 'EventController@index');
    });
});
