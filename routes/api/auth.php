<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'token:user',
    'auth:sanctum',
])->group(function () {
    Route::delete('tokens', 'TokenController@destroy');
});

Route::post('users', 'UserController@store');
Route::post('tokens', 'TokenController@store');
