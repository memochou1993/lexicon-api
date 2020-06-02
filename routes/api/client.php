<?php

use Illuminate\Support\Facades\Route;

Route::get('projects/{project}', 'ProjectController@show');
Route::delete('projects/{project}/cache', 'ProjectCacheController@destroy');
