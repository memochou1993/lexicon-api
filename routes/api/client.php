<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'client',
])->group(function () {
    // TODO: use EventController
    Route::get('projects/{project}', 'ProjectController@show');
    Route::delete('projects/{project}/cache', 'ProjectCacheController@destroy');
});
