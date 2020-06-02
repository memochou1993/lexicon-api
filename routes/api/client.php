<?php

use Illuminate\Support\Facades\Route;

Route::get('project', 'ProjectController@show');
Route::delete('project/cache', 'ProjectCacheController@destroy');
