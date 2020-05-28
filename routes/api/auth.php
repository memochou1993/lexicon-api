<?php

use Illuminate\Support\Facades\Route;

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout')
    ->middleware('auth:sanctum');
