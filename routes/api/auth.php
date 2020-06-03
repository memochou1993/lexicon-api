<?php

use Illuminate\Support\Facades\Route;

Route::post('users', 'UserController@store');
Route::post('tokens', 'TokenController@store');
Route::delete('tokens', 'TokenController@destroy')
    ->middleware(['token:user', 'auth:sanctum']);
