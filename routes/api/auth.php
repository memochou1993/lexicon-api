<?php

use App\Http\Controllers\Api\Auth\TokenController;
use App\Http\Controllers\Api\Auth\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::delete('tokens', [TokenController::class, 'destroy']);
});

Route::post('users', [UserController::class, 'store']);
Route::post('tokens', [TokenController::class, 'store']);
